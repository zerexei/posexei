import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Social\SocialAccountController::edit
* @see app/Http/Controllers/Social/SocialAccountController.php:20
* @route '/settings/connections'
*/
export const edit = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/settings/connections',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Social\SocialAccountController::edit
* @see app/Http/Controllers/Social/SocialAccountController.php:20
* @route '/settings/connections'
*/
edit.url = (options?: RouteQueryOptions) => {
    return edit.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Social\SocialAccountController::edit
* @see app/Http/Controllers/Social/SocialAccountController.php:20
* @route '/settings/connections'
*/
edit.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Social\SocialAccountController::edit
* @see app/Http/Controllers/Social/SocialAccountController.php:20
* @route '/settings/connections'
*/
edit.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Social\SocialAccountController::edit
* @see app/Http/Controllers/Social/SocialAccountController.php:20
* @route '/settings/connections'
*/
const editForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Social\SocialAccountController::edit
* @see app/Http/Controllers/Social/SocialAccountController.php:20
* @route '/settings/connections'
*/
editForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Social\SocialAccountController::edit
* @see app/Http/Controllers/Social/SocialAccountController.php:20
* @route '/settings/connections'
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

/**
* @see \App\Http\Controllers\Social\SocialAccountController::store
* @see app/Http/Controllers/Social/SocialAccountController.php:43
* @route '/settings/connections'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/settings/connections',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Social\SocialAccountController::store
* @see app/Http/Controllers/Social/SocialAccountController.php:43
* @route '/settings/connections'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Social\SocialAccountController::store
* @see app/Http/Controllers/Social/SocialAccountController.php:43
* @route '/settings/connections'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Social\SocialAccountController::store
* @see app/Http/Controllers/Social/SocialAccountController.php:43
* @route '/settings/connections'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Social\SocialAccountController::store
* @see app/Http/Controllers/Social/SocialAccountController.php:43
* @route '/settings/connections'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

const SocialAccountController = { edit, store }

export default SocialAccountController