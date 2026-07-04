import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\CaseNoteController::store
* @see app/Http/Controllers/CaseNoteController.php:15
* @route '/cases/{case}/notes'
*/
export const store = (args: { case: string | { id: string } } | [caseParam: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/cases/{case}/notes',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CaseNoteController::store
* @see app/Http/Controllers/CaseNoteController.php:15
* @route '/cases/{case}/notes'
*/
store.url = (args: { case: string | { id: string } } | [caseParam: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
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

    return store.definition.url
            .replace('{case}', parsedArgs.case.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CaseNoteController::store
* @see app/Http/Controllers/CaseNoteController.php:15
* @route '/cases/{case}/notes'
*/
store.post = (args: { case: string | { id: string } } | [caseParam: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CaseNoteController::store
* @see app/Http/Controllers/CaseNoteController.php:15
* @route '/cases/{case}/notes'
*/
const storeForm = (args: { case: string | { id: string } } | [caseParam: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CaseNoteController::store
* @see app/Http/Controllers/CaseNoteController.php:15
* @route '/cases/{case}/notes'
*/
storeForm.post = (args: { case: string | { id: string } } | [caseParam: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\CaseNoteController::update
* @see app/Http/Controllers/CaseNoteController.php:35
* @route '/cases/{case}/notes/{note}'
*/
export const update = (args: { case: string | { id: string }, note: string | { id: string } } | [caseParam: string | { id: string }, note: string | { id: string } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/cases/{case}/notes/{note}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\CaseNoteController::update
* @see app/Http/Controllers/CaseNoteController.php:35
* @route '/cases/{case}/notes/{note}'
*/
update.url = (args: { case: string | { id: string }, note: string | { id: string } } | [caseParam: string | { id: string }, note: string | { id: string } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            case: args[0],
            note: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        case: typeof args.case === 'object'
        ? args.case.id
        : args.case,
        note: typeof args.note === 'object'
        ? args.note.id
        : args.note,
    }

    return update.definition.url
            .replace('{case}', parsedArgs.case.toString())
            .replace('{note}', parsedArgs.note.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CaseNoteController::update
* @see app/Http/Controllers/CaseNoteController.php:35
* @route '/cases/{case}/notes/{note}'
*/
update.put = (args: { case: string | { id: string }, note: string | { id: string } } | [caseParam: string | { id: string }, note: string | { id: string } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\CaseNoteController::update
* @see app/Http/Controllers/CaseNoteController.php:35
* @route '/cases/{case}/notes/{note}'
*/
const updateForm = (args: { case: string | { id: string }, note: string | { id: string } } | [caseParam: string | { id: string }, note: string | { id: string } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CaseNoteController::update
* @see app/Http/Controllers/CaseNoteController.php:35
* @route '/cases/{case}/notes/{note}'
*/
updateForm.put = (args: { case: string | { id: string }, note: string | { id: string } } | [caseParam: string | { id: string }, note: string | { id: string } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\CaseNoteController::destroy
* @see app/Http/Controllers/CaseNoteController.php:48
* @route '/cases/{case}/notes/{note}'
*/
export const destroy = (args: { case: string | { id: string }, note: string | { id: string } } | [caseParam: string | { id: string }, note: string | { id: string } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/cases/{case}/notes/{note}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\CaseNoteController::destroy
* @see app/Http/Controllers/CaseNoteController.php:48
* @route '/cases/{case}/notes/{note}'
*/
destroy.url = (args: { case: string | { id: string }, note: string | { id: string } } | [caseParam: string | { id: string }, note: string | { id: string } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            case: args[0],
            note: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        case: typeof args.case === 'object'
        ? args.case.id
        : args.case,
        note: typeof args.note === 'object'
        ? args.note.id
        : args.note,
    }

    return destroy.definition.url
            .replace('{case}', parsedArgs.case.toString())
            .replace('{note}', parsedArgs.note.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CaseNoteController::destroy
* @see app/Http/Controllers/CaseNoteController.php:48
* @route '/cases/{case}/notes/{note}'
*/
destroy.delete = (args: { case: string | { id: string }, note: string | { id: string } } | [caseParam: string | { id: string }, note: string | { id: string } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\CaseNoteController::destroy
* @see app/Http/Controllers/CaseNoteController.php:48
* @route '/cases/{case}/notes/{note}'
*/
const destroyForm = (args: { case: string | { id: string }, note: string | { id: string } } | [caseParam: string | { id: string }, note: string | { id: string } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CaseNoteController::destroy
* @see app/Http/Controllers/CaseNoteController.php:48
* @route '/cases/{case}/notes/{note}'
*/
destroyForm.delete = (args: { case: string | { id: string }, note: string | { id: string } } | [caseParam: string | { id: string }, note: string | { id: string } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const CaseNoteController = { store, update, destroy }

export default CaseNoteController