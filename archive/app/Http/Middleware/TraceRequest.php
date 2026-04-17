<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use OpenTelemetry\API\Globals;
use OpenTelemetry\SemConv\TraceAttributes;
use Symfony\Component\HttpFoundation\Response;

class TraceRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tracer = Globals::tracerProvider()->getTracer('laravel-http');
        
        $span = $tracer->spanBuilder($request->method() . ' ' . $request->path())
            ->setSpanKind(\OpenTelemetry\API\Trace\SpanKind::KIND_SERVER)
            ->setAttribute(TraceAttributes::HTTP_METHOD, $request->method())
            ->setAttribute(TraceAttributes::HTTP_URL, $request->fullUrl())
            ->setAttribute(TraceAttributes::HTTP_TARGET, $request->getRequestUri())
            ->setAttribute(TraceAttributes::HTTP_HOST, $request->getHost())
            ->setAttribute(TraceAttributes::HTTP_SCHEME, $request->getScheme())
            ->setAttribute(TraceAttributes::HTTP_USER_AGENT, $request->userAgent())
            ->setAttribute(TraceAttributes::HTTP_CLIENT_IP, $request->ip())
            ->startSpan();

        $scope = $span->activate();

        try {
            $response = $next($request);

            $span->setAttribute(TraceAttributes::HTTP_STATUS_CODE, $response->getStatusCode());
            
            if ($response->isServerError()) {
                $span->setStatus(\OpenTelemetry\API\Trace\StatusCode::STATUS_ERROR);
            }

            return $response;
        } catch (\Throwable $e) {
            $span->recordException($e);
            $span->setStatus(\OpenTelemetry\API\Trace\StatusCode::STATUS_ERROR);
            throw $e;
        } finally {
            $span->end();
            $scope->detach();
        }
    }
}
