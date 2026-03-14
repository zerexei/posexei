<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use OpenTelemetry\API\Globals;
use OpenTelemetry\API\Trace\Propagation\TraceContextPropagator;
use OpenTelemetry\SDK\Common\Attribute\Attributes;
use OpenTelemetry\SDK\Common\Configuration\Configuration;
use OpenTelemetry\SDK\Common\Export\Http\PsrTransportFactory;
use OpenTelemetry\SDK\Resource\ResourceInfo;
use OpenTelemetry\SDK\Resource\ResourceInfoFactory;
use OpenTelemetry\SDK\Trace\Sampler\AlwaysOnSampler;
use OpenTelemetry\SDK\Trace\SpanProcessor\BatchSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;
use OpenTelemetry\SDK\Trace\TracerProviderInterface;
use OpenTelemetry\SemConv\ResourceAttributes;
use OpenTelemetry\Contrib\OTLP\SpanExporter;

class TelemetryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(TracerProviderInterface::class, function () {
            $endpoint = env('OTEL_EXPORTER_OTLP_ENDPOINT', 'http://otel-collector:4318');
            
            $exporter = new SpanExporter(
                (new PsrTransportFactory())->create($endpoint . '/v1/traces', 'application/x-protobuf')
            );

            $resource = ResourceInfoFactory::merge(
                ResourceInfoFactory::defaultResource(),
                ResourceInfo::create(Attributes::create([
                    ResourceAttributes::SERVICE_NAME => env('OTEL_SERVICE_NAME', 'laravel-app'),
                    ResourceAttributes::DEPLOYMENT_ENVIRONMENT => app()->environment(),
                ]))
            );

            $tracerProvider = TracerProvider::builder()
                ->addSpanProcessor(new BatchSpanProcessor($exporter))
                ->setResource($resource)
                ->setSampler(new AlwaysOnSampler())
                ->build();

            return $tracerProvider;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        /** @var TracerProviderInterface $tracerProvider */
        $tracerProvider = $this->app->make(TracerProviderInterface::class);

        Globals::setTracerProvider($tracerProvider);
        Globals::setPropagator(TraceContextPropagator::getInstance());

        $this->instrumentDatabase();
        $this->instrumentQueue();
        $this->instrumentRedis();
    }

    protected function instrumentDatabase(): void
    {
        \Illuminate\Support\Facades\DB::listen(function ($query) {
            $tracer = Globals::tracerProvider()->getTracer('laravel-database');
            $span = $tracer->spanBuilder('sql ' . $query->connectionName)
                ->setAttribute('db.system', $query->connection->getDriverName())
                ->setAttribute('db.statement', $query->sql)
                ->setAttribute('db.time', $query->time)
                ->startSpan();
            $span->end();
        });
    }

    protected function instrumentQueue(): void
    {
        \Illuminate\Support\Facades\Queue::before(function (\Illuminate\Queue\Events\JobProcessing $event) {
            $tracer = Globals::tracerProvider()->getTracer('laravel-queue');
            $tracer->spanBuilder('queue ' . $event->job->getName())
                ->setAttribute('messaging.system', 'laravel')
                ->setAttribute('messaging.destination', $event->job->getQueue())
                ->startSpan();
        });

        \Illuminate\Support\Facades\Queue::after(function (\Illuminate\Queue\Events\JobProcessed $event) {
            $span = \OpenTelemetry\API\Trace\Span::getCurrent();
            $span->end();
        });

        \Illuminate\Support\Facades\Queue::failing(function (\Illuminate\Queue\Events\JobFailed $event) {
            $span = \OpenTelemetry\API\Trace\Span::getCurrent();
            $span->recordException($event->exception);
            $span->setStatus(\OpenTelemetry\API\Trace\StatusCode::STATUS_ERROR);
            $span->end();
        });
    }

    protected function instrumentRedis(): void
    {
        \Illuminate\Support\Facades\Redis::enableEvents();
        \Illuminate\Support\Facades\Redis::listen(function ($event) {
            $tracer = Globals::tracerProvider()->getTracer('laravel-redis');
            $span = $tracer->spanBuilder('redis ' . $event->command)
                ->setAttribute('db.system', 'redis')
                ->setAttribute('db.statement', $event->command)
                ->startSpan();
            $span->end();
        });
    }
}
