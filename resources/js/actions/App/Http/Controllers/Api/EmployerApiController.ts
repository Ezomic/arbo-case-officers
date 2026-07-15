import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\EmployerApiController::show
* @see app/Http/Controllers/Api/EmployerApiController.php:16
* @route '/api/employers/{employer}'
*/
export const show = (args: { employer: string | number } | [employer: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/employers/{employer}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\EmployerApiController::show
* @see app/Http/Controllers/Api/EmployerApiController.php:16
* @route '/api/employers/{employer}'
*/
show.url = (args: { employer: string | number } | [employer: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return show.definition.url
            .replace('{employer}', parsedArgs.employer.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\EmployerApiController::show
* @see app/Http/Controllers/Api/EmployerApiController.php:16
* @route '/api/employers/{employer}'
*/
show.get = (args: { employer: string | number } | [employer: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\EmployerApiController::show
* @see app/Http/Controllers/Api/EmployerApiController.php:16
* @route '/api/employers/{employer}'
*/
show.head = (args: { employer: string | number } | [employer: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\EmployerApiController::show
* @see app/Http/Controllers/Api/EmployerApiController.php:16
* @route '/api/employers/{employer}'
*/
const showForm = (args: { employer: string | number } | [employer: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\EmployerApiController::show
* @see app/Http/Controllers/Api/EmployerApiController.php:16
* @route '/api/employers/{employer}'
*/
showForm.get = (args: { employer: string | number } | [employer: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\EmployerApiController::show
* @see app/Http/Controllers/Api/EmployerApiController.php:16
* @route '/api/employers/{employer}'
*/
showForm.head = (args: { employer: string | number } | [employer: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

const EmployerApiController = { show }

export default EmployerApiController