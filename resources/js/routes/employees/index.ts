import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\EmployeeController::index
* @see app/Http/Controllers/EmployeeController.php:15
* @route '/employees'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/employees',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\EmployeeController::index
* @see app/Http/Controllers/EmployeeController.php:15
* @route '/employees'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\EmployeeController::index
* @see app/Http/Controllers/EmployeeController.php:15
* @route '/employees'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EmployeeController::index
* @see app/Http/Controllers/EmployeeController.php:15
* @route '/employees'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\EmployeeController::index
* @see app/Http/Controllers/EmployeeController.php:15
* @route '/employees'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EmployeeController::index
* @see app/Http/Controllers/EmployeeController.php:15
* @route '/employees'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EmployeeController::index
* @see app/Http/Controllers/EmployeeController.php:15
* @route '/employees'
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
* @see \App\Http\Controllers\EmployeeController::store
* @see app/Http/Controllers/EmployeeController.php:25
* @route '/employers/{employer}/employees'
*/
export const store = (args: { employer: string | { id: string } } | [employer: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/employers/{employer}/employees',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\EmployeeController::store
* @see app/Http/Controllers/EmployeeController.php:25
* @route '/employers/{employer}/employees'
*/
store.url = (args: { employer: string | { id: string } } | [employer: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
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

    return store.definition.url
            .replace('{employer}', parsedArgs.employer.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\EmployeeController::store
* @see app/Http/Controllers/EmployeeController.php:25
* @route '/employers/{employer}/employees'
*/
store.post = (args: { employer: string | { id: string } } | [employer: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\EmployeeController::store
* @see app/Http/Controllers/EmployeeController.php:25
* @route '/employers/{employer}/employees'
*/
const storeForm = (args: { employer: string | { id: string } } | [employer: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\EmployeeController::store
* @see app/Http/Controllers/EmployeeController.php:25
* @route '/employers/{employer}/employees'
*/
storeForm.post = (args: { employer: string | { id: string } } | [employer: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

const employees = {
    index: Object.assign(index, index),
    store: Object.assign(store, store),
}

export default employees