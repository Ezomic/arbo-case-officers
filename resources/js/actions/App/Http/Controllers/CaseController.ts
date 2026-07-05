import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\CaseController::index
* @see app/Http/Controllers/CaseController.php:15
* @route '/cases'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/cases',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CaseController::index
* @see app/Http/Controllers/CaseController.php:15
* @route '/cases'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CaseController::index
* @see app/Http/Controllers/CaseController.php:15
* @route '/cases'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CaseController::index
* @see app/Http/Controllers/CaseController.php:15
* @route '/cases'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CaseController::index
* @see app/Http/Controllers/CaseController.php:15
* @route '/cases'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CaseController::index
* @see app/Http/Controllers/CaseController.php:15
* @route '/cases'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CaseController::index
* @see app/Http/Controllers/CaseController.php:15
* @route '/cases'
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
* @see \App\Http\Controllers\CaseController::store
* @see app/Http/Controllers/CaseController.php:41
* @route '/cases'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/cases',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CaseController::store
* @see app/Http/Controllers/CaseController.php:41
* @route '/cases'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CaseController::store
* @see app/Http/Controllers/CaseController.php:41
* @route '/cases'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CaseController::store
* @see app/Http/Controllers/CaseController.php:41
* @route '/cases'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CaseController::store
* @see app/Http/Controllers/CaseController.php:41
* @route '/cases'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\CaseController::show
* @see app/Http/Controllers/CaseController.php:29
* @route '/cases/{case}'
*/
export const show = (args: { case: string | number | { id: string | number } } | [caseParam: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/cases/{case}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CaseController::show
* @see app/Http/Controllers/CaseController.php:29
* @route '/cases/{case}'
*/
show.url = (args: { case: string | number | { id: string | number } } | [caseParam: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { case: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { case: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            case: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        case: typeof args.case === 'object'
        ? args.case.id
        : args.case,
    }

    return show.definition.url
            .replace('{case}', parsedArgs.case.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CaseController::show
* @see app/Http/Controllers/CaseController.php:29
* @route '/cases/{case}'
*/
show.get = (args: { case: string | number | { id: string | number } } | [caseParam: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CaseController::show
* @see app/Http/Controllers/CaseController.php:29
* @route '/cases/{case}'
*/
show.head = (args: { case: string | number | { id: string | number } } | [caseParam: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CaseController::show
* @see app/Http/Controllers/CaseController.php:29
* @route '/cases/{case}'
*/
const showForm = (args: { case: string | number | { id: string | number } } | [caseParam: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CaseController::show
* @see app/Http/Controllers/CaseController.php:29
* @route '/cases/{case}'
*/
showForm.get = (args: { case: string | number | { id: string | number } } | [caseParam: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CaseController::show
* @see app/Http/Controllers/CaseController.php:29
* @route '/cases/{case}'
*/
showForm.head = (args: { case: string | number | { id: string | number } } | [caseParam: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

const CaseController = { index, store, show }

export default CaseController