import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\CaseApiController::store
* @see app/Http/Controllers/Api/CaseApiController.php:19
* @route '/api/cases'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/cases',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\CaseApiController::store
* @see app/Http/Controllers/Api/CaseApiController.php:19
* @route '/api/cases'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\CaseApiController::store
* @see app/Http/Controllers/Api/CaseApiController.php:19
* @route '/api/cases'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\CaseApiController::store
* @see app/Http/Controllers/Api/CaseApiController.php:19
* @route '/api/cases'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\CaseApiController::store
* @see app/Http/Controllers/Api/CaseApiController.php:19
* @route '/api/cases'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Api\CaseApiController::index
* @see app/Http/Controllers/Api/CaseApiController.php:46
* @route '/api/cases'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/cases',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\CaseApiController::index
* @see app/Http/Controllers/Api/CaseApiController.php:46
* @route '/api/cases'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\CaseApiController::index
* @see app/Http/Controllers/Api/CaseApiController.php:46
* @route '/api/cases'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\CaseApiController::index
* @see app/Http/Controllers/Api/CaseApiController.php:46
* @route '/api/cases'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\CaseApiController::index
* @see app/Http/Controllers/Api/CaseApiController.php:46
* @route '/api/cases'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\CaseApiController::index
* @see app/Http/Controllers/Api/CaseApiController.php:46
* @route '/api/cases'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\CaseApiController::index
* @see app/Http/Controllers/Api/CaseApiController.php:46
* @route '/api/cases'
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
* @see \App\Http\Controllers\Api\CaseApiController::show
* @see app/Http/Controllers/Api/CaseApiController.php:58
* @route '/api/cases/{case}'
*/
export const show = (args: { case: string | number } | [caseParam: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/cases/{case}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\CaseApiController::show
* @see app/Http/Controllers/Api/CaseApiController.php:58
* @route '/api/cases/{case}'
*/
show.url = (args: { case: string | number } | [caseParam: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { case: args }
    }

    if (Array.isArray(args)) {
        args = {
            case: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        case: args.case,
    }

    return show.definition.url
            .replace('{case}', parsedArgs.case.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\CaseApiController::show
* @see app/Http/Controllers/Api/CaseApiController.php:58
* @route '/api/cases/{case}'
*/
show.get = (args: { case: string | number } | [caseParam: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\CaseApiController::show
* @see app/Http/Controllers/Api/CaseApiController.php:58
* @route '/api/cases/{case}'
*/
show.head = (args: { case: string | number } | [caseParam: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\CaseApiController::show
* @see app/Http/Controllers/Api/CaseApiController.php:58
* @route '/api/cases/{case}'
*/
const showForm = (args: { case: string | number } | [caseParam: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\CaseApiController::show
* @see app/Http/Controllers/Api/CaseApiController.php:58
* @route '/api/cases/{case}'
*/
showForm.get = (args: { case: string | number } | [caseParam: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\CaseApiController::show
* @see app/Http/Controllers/Api/CaseApiController.php:58
* @route '/api/cases/{case}'
*/
showForm.head = (args: { case: string | number } | [caseParam: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

/**
* @see \App\Http\Controllers\Api\CaseApiController::update
* @see app/Http/Controllers/Api/CaseApiController.php:113
* @route '/api/cases/{case}'
*/
export const update = (args: { case: string | number } | [caseParam: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/api/cases/{case}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Api\CaseApiController::update
* @see app/Http/Controllers/Api/CaseApiController.php:113
* @route '/api/cases/{case}'
*/
update.url = (args: { case: string | number } | [caseParam: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { case: args }
    }

    if (Array.isArray(args)) {
        args = {
            case: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        case: args.case,
    }

    return update.definition.url
            .replace('{case}', parsedArgs.case.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\CaseApiController::update
* @see app/Http/Controllers/Api/CaseApiController.php:113
* @route '/api/cases/{case}'
*/
update.put = (args: { case: string | number } | [caseParam: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Api\CaseApiController::update
* @see app/Http/Controllers/Api/CaseApiController.php:113
* @route '/api/cases/{case}'
*/
const updateForm = (args: { case: string | number } | [caseParam: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\CaseApiController::update
* @see app/Http/Controllers/Api/CaseApiController.php:113
* @route '/api/cases/{case}'
*/
updateForm.put = (args: { case: string | number } | [caseParam: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\Api\CaseApiController::mutate
* @see app/Http/Controllers/Api/CaseApiController.php:68
* @route '/api/cases/{case}/mutate'
*/
export const mutate = (args: { case: string | number } | [caseParam: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: mutate.url(args, options),
    method: 'post',
})

mutate.definition = {
    methods: ["post"],
    url: '/api/cases/{case}/mutate',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\CaseApiController::mutate
* @see app/Http/Controllers/Api/CaseApiController.php:68
* @route '/api/cases/{case}/mutate'
*/
mutate.url = (args: { case: string | number } | [caseParam: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { case: args }
    }

    if (Array.isArray(args)) {
        args = {
            case: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        case: args.case,
    }

    return mutate.definition.url
            .replace('{case}', parsedArgs.case.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\CaseApiController::mutate
* @see app/Http/Controllers/Api/CaseApiController.php:68
* @route '/api/cases/{case}/mutate'
*/
mutate.post = (args: { case: string | number } | [caseParam: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: mutate.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\CaseApiController::mutate
* @see app/Http/Controllers/Api/CaseApiController.php:68
* @route '/api/cases/{case}/mutate'
*/
const mutateForm = (args: { case: string | number } | [caseParam: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: mutate.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\CaseApiController::mutate
* @see app/Http/Controllers/Api/CaseApiController.php:68
* @route '/api/cases/{case}/mutate'
*/
mutateForm.post = (args: { case: string | number } | [caseParam: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: mutate.url(args, options),
    method: 'post',
})

mutate.form = mutateForm

/**
* @see \App\Http\Controllers\Api\CaseApiController::close
* @see app/Http/Controllers/Api/CaseApiController.php:87
* @route '/api/cases/{case}/close'
*/
export const close = (args: { case: string | number } | [caseParam: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: close.url(args, options),
    method: 'post',
})

close.definition = {
    methods: ["post"],
    url: '/api/cases/{case}/close',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\CaseApiController::close
* @see app/Http/Controllers/Api/CaseApiController.php:87
* @route '/api/cases/{case}/close'
*/
close.url = (args: { case: string | number } | [caseParam: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { case: args }
    }

    if (Array.isArray(args)) {
        args = {
            case: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        case: args.case,
    }

    return close.definition.url
            .replace('{case}', parsedArgs.case.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\CaseApiController::close
* @see app/Http/Controllers/Api/CaseApiController.php:87
* @route '/api/cases/{case}/close'
*/
close.post = (args: { case: string | number } | [caseParam: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: close.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\CaseApiController::close
* @see app/Http/Controllers/Api/CaseApiController.php:87
* @route '/api/cases/{case}/close'
*/
const closeForm = (args: { case: string | number } | [caseParam: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: close.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\CaseApiController::close
* @see app/Http/Controllers/Api/CaseApiController.php:87
* @route '/api/cases/{case}/close'
*/
closeForm.post = (args: { case: string | number } | [caseParam: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: close.url(args, options),
    method: 'post',
})

close.form = closeForm

const CaseApiController = { store, index, show, update, mutate, close }

export default CaseApiController