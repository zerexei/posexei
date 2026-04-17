import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Settings\BillingController::edit
* @see app/Http/Controllers/Settings/BillingController.php:12
* @route '/settings/billing'
*/
export const edit = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/settings/billing',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Settings\BillingController::edit
* @see app/Http/Controllers/Settings/BillingController.php:12
* @route '/settings/billing'
*/
edit.url = (options?: RouteQueryOptions) => {
    return edit.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\BillingController::edit
* @see app/Http/Controllers/Settings/BillingController.php:12
* @route '/settings/billing'
*/
edit.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Settings\BillingController::edit
* @see app/Http/Controllers/Settings/BillingController.php:12
* @route '/settings/billing'
*/
edit.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Settings\BillingController::edit
* @see app/Http/Controllers/Settings/BillingController.php:12
* @route '/settings/billing'
*/
const editForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Settings\BillingController::edit
* @see app/Http/Controllers/Settings/BillingController.php:12
* @route '/settings/billing'
*/
editForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Settings\BillingController::edit
* @see app/Http/Controllers/Settings/BillingController.php:12
* @route '/settings/billing'
*/
editForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

edit.form = editForm

const billing = {
    edit: Object.assign(edit, edit),
}

export default billing