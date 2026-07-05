import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\EmployeeApiController::index
* @see app/Http/Controllers/Api/EmployeeApiController.php:17
* @route '/api/employers/{employer}/employees'
*/
export const index = (args: { employer: string | number } | [employer: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/employers/{employer}/employees',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\EmployeeApiController::index
* @see app/Http/Controllers/Api/EmployeeApiController.php:17
* @route '/api/employers/{employer}/employees'
*/
index.url = (args: { employer: string | number } | [employer: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { employer: args }
    }

    if (Array.isArray(args)) {
        args = {
            employer: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        employer: args.employer,
    }

    return index.definition.url
            .replace('{employer}', parsedArgs.employer.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\EmployeeApiController::index
* @see app/Http/Controllers/Api/EmployeeApiController.php:17
* @route '/api/employers/{employer}/employees'
*/
index.get = (args: { employer: string | number } | [employer: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\EmployeeApiController::index
* @see app/Http/Controllers/Api/EmployeeApiController.php:17
* @route '/api/employers/{employer}/employees'
*/
index.head = (args: { employer: string | number } | [employer: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\EmployeeApiController::index
* @see app/Http/Controllers/Api/EmployeeApiController.php:17
* @route '/api/employers/{employer}/employees'
*/
const indexForm = (args: { employer: string | number } | [employer: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\EmployeeApiController::index
* @see app/Http/Controllers/Api/EmployeeApiController.php:17
* @route '/api/employers/{employer}/employees'
*/
indexForm.get = (args: { employer: string | number } | [employer: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\EmployeeApiController::index
* @see app/Http/Controllers/Api/EmployeeApiController.php:17
* @route '/api/employers/{employer}/employees'
*/
indexForm.head = (args: { employer: string | number } | [employer: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\Api\EmployeeApiController::store
* @see app/Http/Controllers/Api/EmployeeApiController.php:28
* @route '/api/employers/{employer}/employees'
*/
export const store = (args: { employer: string | number } | [employer: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/employers/{employer}/employees',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\EmployeeApiController::store
* @see app/Http/Controllers/Api/EmployeeApiController.php:28
* @route '/api/employers/{employer}/employees'
*/
store.url = (args: { employer: string | number } | [employer: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { employer: args }
    }

    if (Array.isArray(args)) {
        args = {
            employer: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        employer: args.employer,
    }

    return store.definition.url
            .replace('{employer}', parsedArgs.employer.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\EmployeeApiController::store
* @see app/Http/Controllers/Api/EmployeeApiController.php:28
* @route '/api/employers/{employer}/employees'
*/
store.post = (args: { employer: string | number } | [employer: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\EmployeeApiController::store
* @see app/Http/Controllers/Api/EmployeeApiController.php:28
* @route '/api/employers/{employer}/employees'
*/
const storeForm = (args: { employer: string | number } | [employer: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\EmployeeApiController::store
* @see app/Http/Controllers/Api/EmployeeApiController.php:28
* @route '/api/employers/{employer}/employees'
*/
storeForm.post = (args: { employer: string | number } | [employer: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

const EmployeeApiController = { index, store }

export default EmployeeApiController