import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\EmployerController::index
* @see app/Http/Controllers/EmployerController.php:16
* @route '/employers'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/employers',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\EmployerController::index
* @see app/Http/Controllers/EmployerController.php:16
* @route '/employers'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\EmployerController::index
* @see app/Http/Controllers/EmployerController.php:16
* @route '/employers'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EmployerController::index
* @see app/Http/Controllers/EmployerController.php:16
* @route '/employers'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\EmployerController::index
* @see app/Http/Controllers/EmployerController.php:16
* @route '/employers'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EmployerController::index
* @see app/Http/Controllers/EmployerController.php:16
* @route '/employers'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EmployerController::index
* @see app/Http/Controllers/EmployerController.php:16
* @route '/employers'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\EmployerController::store
* @see app/Http/Controllers/EmployerController.php:23
* @route '/employers'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/employers',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\EmployerController::store
* @see app/Http/Controllers/EmployerController.php:23
* @route '/employers'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\EmployerController::store
* @see app/Http/Controllers/EmployerController.php:23
* @route '/employers'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\EmployerController::store
* @see app/Http/Controllers/EmployerController.php:23
* @route '/employers'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\EmployerController::store
* @see app/Http/Controllers/EmployerController.php:23
* @route '/employers'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\EmployerController::show
* @see app/Http/Controllers/EmployerController.php:37
* @route '/employers/{employer}'
*/
export const show = (args: { employer: string | { id: string } } | [employer: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/employers/{employer}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\EmployerController::show
* @see app/Http/Controllers/EmployerController.php:37
* @route '/employers/{employer}'
*/
show.url = (args: { employer: string | { id: string } } | [employer: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { employer: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { employer: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            employer: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        employer: typeof args.employer === 'object'
        ? args.employer.id
        : args.employer,
    }

    return show.definition.url
            .replace('{employer}', parsedArgs.employer.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\EmployerController::show
* @see app/Http/Controllers/EmployerController.php:37
* @route '/employers/{employer}'
*/
show.get = (args: { employer: string | { id: string } } | [employer: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EmployerController::show
* @see app/Http/Controllers/EmployerController.php:37
* @route '/employers/{employer}'
*/
show.head = (args: { employer: string | { id: string } } | [employer: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\EmployerController::show
* @see app/Http/Controllers/EmployerController.php:37
* @route '/employers/{employer}'
*/
const showForm = (args: { employer: string | { id: string } } | [employer: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EmployerController::show
* @see app/Http/Controllers/EmployerController.php:37
* @route '/employers/{employer}'
*/
showForm.get = (args: { employer: string | { id: string } } | [employer: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EmployerController::show
* @see app/Http/Controllers/EmployerController.php:37
* @route '/employers/{employer}'
*/
showForm.head = (args: { employer: string | { id: string } } | [employer: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

const EmployerController = { index, store, show }

export default EmployerController