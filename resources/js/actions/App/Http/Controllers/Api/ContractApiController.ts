import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\ContractApiController::index
* @see app/Http/Controllers/Api/ContractApiController.php:12
* @route '/api/employers/{employer}/contracts'
*/
export const index = (args: { employer: string | number } | [employer: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/employers/{employer}/contracts',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ContractApiController::index
* @see app/Http/Controllers/Api/ContractApiController.php:12
* @route '/api/employers/{employer}/contracts'
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
* @see \App\Http\Controllers\Api\ContractApiController::index
* @see app/Http/Controllers/Api/ContractApiController.php:12
* @route '/api/employers/{employer}/contracts'
*/
index.get = (args: { employer: string | number } | [employer: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ContractApiController::index
* @see app/Http/Controllers/Api/ContractApiController.php:12
* @route '/api/employers/{employer}/contracts'
*/
index.head = (args: { employer: string | number } | [employer: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ContractApiController::index
* @see app/Http/Controllers/Api/ContractApiController.php:12
* @route '/api/employers/{employer}/contracts'
*/
const indexForm = (args: { employer: string | number } | [employer: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ContractApiController::index
* @see app/Http/Controllers/Api/ContractApiController.php:12
* @route '/api/employers/{employer}/contracts'
*/
indexForm.get = (args: { employer: string | number } | [employer: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ContractApiController::index
* @see app/Http/Controllers/Api/ContractApiController.php:12
* @route '/api/employers/{employer}/contracts'
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

const ContractApiController = { index }

export default ContractApiController