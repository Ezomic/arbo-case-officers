import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\CaseTaskController::store
* @see app/Http/Controllers/CaseTaskController.php:14
* @route '/cases/{case}/tasks'
*/
export const store = (args: { case: string | { id: string } } | [caseParam: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/cases/{case}/tasks',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CaseTaskController::store
* @see app/Http/Controllers/CaseTaskController.php:14
* @route '/cases/{case}/tasks'
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
* @see \App\Http\Controllers\CaseTaskController::store
* @see app/Http/Controllers/CaseTaskController.php:14
* @route '/cases/{case}/tasks'
*/
store.post = (args: { case: string | { id: string } } | [caseParam: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CaseTaskController::store
* @see app/Http/Controllers/CaseTaskController.php:14
* @route '/cases/{case}/tasks'
*/
const storeForm = (args: { case: string | { id: string } } | [caseParam: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CaseTaskController::store
* @see app/Http/Controllers/CaseTaskController.php:14
* @route '/cases/{case}/tasks'
*/
storeForm.post = (args: { case: string | { id: string } } | [caseParam: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\CaseTaskController::update
* @see app/Http/Controllers/CaseTaskController.php:34
* @route '/cases/{case}/tasks/{task}'
*/
export const update = (args: { case: string | { id: string }, task: string | { id: string } } | [caseParam: string | { id: string }, task: string | { id: string } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/cases/{case}/tasks/{task}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\CaseTaskController::update
* @see app/Http/Controllers/CaseTaskController.php:34
* @route '/cases/{case}/tasks/{task}'
*/
update.url = (args: { case: string | { id: string }, task: string | { id: string } } | [caseParam: string | { id: string }, task: string | { id: string } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            case: args[0],
            task: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        case: typeof args.case === 'object'
        ? args.case.id
        : args.case,
        task: typeof args.task === 'object'
        ? args.task.id
        : args.task,
    }

    return update.definition.url
            .replace('{case}', parsedArgs.case.toString())
            .replace('{task}', parsedArgs.task.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CaseTaskController::update
* @see app/Http/Controllers/CaseTaskController.php:34
* @route '/cases/{case}/tasks/{task}'
*/
update.put = (args: { case: string | { id: string }, task: string | { id: string } } | [caseParam: string | { id: string }, task: string | { id: string } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\CaseTaskController::update
* @see app/Http/Controllers/CaseTaskController.php:34
* @route '/cases/{case}/tasks/{task}'
*/
const updateForm = (args: { case: string | { id: string }, task: string | { id: string } } | [caseParam: string | { id: string }, task: string | { id: string } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CaseTaskController::update
* @see app/Http/Controllers/CaseTaskController.php:34
* @route '/cases/{case}/tasks/{task}'
*/
updateForm.put = (args: { case: string | { id: string }, task: string | { id: string } } | [caseParam: string | { id: string }, task: string | { id: string } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\CaseTaskController::complete
* @see app/Http/Controllers/CaseTaskController.php:50
* @route '/cases/{case}/tasks/{task}/complete'
*/
export const complete = (args: { case: string | { id: string }, task: string | { id: string } } | [caseParam: string | { id: string }, task: string | { id: string } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: complete.url(args, options),
    method: 'post',
})

complete.definition = {
    methods: ["post"],
    url: '/cases/{case}/tasks/{task}/complete',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CaseTaskController::complete
* @see app/Http/Controllers/CaseTaskController.php:50
* @route '/cases/{case}/tasks/{task}/complete'
*/
complete.url = (args: { case: string | { id: string }, task: string | { id: string } } | [caseParam: string | { id: string }, task: string | { id: string } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            case: args[0],
            task: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        case: typeof args.case === 'object'
        ? args.case.id
        : args.case,
        task: typeof args.task === 'object'
        ? args.task.id
        : args.task,
    }

    return complete.definition.url
            .replace('{case}', parsedArgs.case.toString())
            .replace('{task}', parsedArgs.task.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CaseTaskController::complete
* @see app/Http/Controllers/CaseTaskController.php:50
* @route '/cases/{case}/tasks/{task}/complete'
*/
complete.post = (args: { case: string | { id: string }, task: string | { id: string } } | [caseParam: string | { id: string }, task: string | { id: string } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: complete.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CaseTaskController::complete
* @see app/Http/Controllers/CaseTaskController.php:50
* @route '/cases/{case}/tasks/{task}/complete'
*/
const completeForm = (args: { case: string | { id: string }, task: string | { id: string } } | [caseParam: string | { id: string }, task: string | { id: string } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: complete.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CaseTaskController::complete
* @see app/Http/Controllers/CaseTaskController.php:50
* @route '/cases/{case}/tasks/{task}/complete'
*/
completeForm.post = (args: { case: string | { id: string }, task: string | { id: string } } | [caseParam: string | { id: string }, task: string | { id: string } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: complete.url(args, options),
    method: 'post',
})

complete.form = completeForm

/**
* @see \App\Http\Controllers\CaseTaskController::destroy
* @see app/Http/Controllers/CaseTaskController.php:59
* @route '/cases/{case}/tasks/{task}'
*/
export const destroy = (args: { case: string | { id: string }, task: string | { id: string } } | [caseParam: string | { id: string }, task: string | { id: string } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/cases/{case}/tasks/{task}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\CaseTaskController::destroy
* @see app/Http/Controllers/CaseTaskController.php:59
* @route '/cases/{case}/tasks/{task}'
*/
destroy.url = (args: { case: string | { id: string }, task: string | { id: string } } | [caseParam: string | { id: string }, task: string | { id: string } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            case: args[0],
            task: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        case: typeof args.case === 'object'
        ? args.case.id
        : args.case,
        task: typeof args.task === 'object'
        ? args.task.id
        : args.task,
    }

    return destroy.definition.url
            .replace('{case}', parsedArgs.case.toString())
            .replace('{task}', parsedArgs.task.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CaseTaskController::destroy
* @see app/Http/Controllers/CaseTaskController.php:59
* @route '/cases/{case}/tasks/{task}'
*/
destroy.delete = (args: { case: string | { id: string }, task: string | { id: string } } | [caseParam: string | { id: string }, task: string | { id: string } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\CaseTaskController::destroy
* @see app/Http/Controllers/CaseTaskController.php:59
* @route '/cases/{case}/tasks/{task}'
*/
const destroyForm = (args: { case: string | { id: string }, task: string | { id: string } } | [caseParam: string | { id: string }, task: string | { id: string } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CaseTaskController::destroy
* @see app/Http/Controllers/CaseTaskController.php:59
* @route '/cases/{case}/tasks/{task}'
*/
destroyForm.delete = (args: { case: string | { id: string }, task: string | { id: string } } | [caseParam: string | { id: string }, task: string | { id: string } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const caseTasks = {
    store: Object.assign(store, store),
    update: Object.assign(update, update),
    complete: Object.assign(complete, complete),
    destroy: Object.assign(destroy, destroy),
}

export default caseTasks