import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\ContactPersonController::store
* @see app/Http/Controllers/ContactPersonController.php:14
* @route '/employers/{employer}/contact-persons'
*/
export const store = (args: { employer: string | { id: string } } | [employer: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/employers/{employer}/contact-persons',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ContactPersonController::store
* @see app/Http/Controllers/ContactPersonController.php:14
* @route '/employers/{employer}/contact-persons'
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
* @see \App\Http\Controllers\ContactPersonController::store
* @see app/Http/Controllers/ContactPersonController.php:14
* @route '/employers/{employer}/contact-persons'
*/
store.post = (args: { employer: string | { id: string } } | [employer: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ContactPersonController::store
* @see app/Http/Controllers/ContactPersonController.php:14
* @route '/employers/{employer}/contact-persons'
*/
const storeForm = (args: { employer: string | { id: string } } | [employer: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ContactPersonController::store
* @see app/Http/Controllers/ContactPersonController.php:14
* @route '/employers/{employer}/contact-persons'
*/
storeForm.post = (args: { employer: string | { id: string } } | [employer: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\ContactPersonController::update
* @see app/Http/Controllers/ContactPersonController.php:30
* @route '/employers/{employer}/contact-persons/{contactPerson}'
*/
export const update = (args: { employer: string | { id: string }, contactPerson: string | { id: string } } | [employer: string | { id: string }, contactPerson: string | { id: string } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/employers/{employer}/contact-persons/{contactPerson}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\ContactPersonController::update
* @see app/Http/Controllers/ContactPersonController.php:30
* @route '/employers/{employer}/contact-persons/{contactPerson}'
*/
update.url = (args: { employer: string | { id: string }, contactPerson: string | { id: string } } | [employer: string | { id: string }, contactPerson: string | { id: string } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            employer: args[0],
            contactPerson: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        employer: typeof args.employer === 'object'
        ? args.employer.id
        : args.employer,
        contactPerson: typeof args.contactPerson === 'object'
        ? args.contactPerson.id
        : args.contactPerson,
    }

    return update.definition.url
            .replace('{employer}', parsedArgs.employer.toString())
            .replace('{contactPerson}', parsedArgs.contactPerson.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContactPersonController::update
* @see app/Http/Controllers/ContactPersonController.php:30
* @route '/employers/{employer}/contact-persons/{contactPerson}'
*/
update.put = (args: { employer: string | { id: string }, contactPerson: string | { id: string } } | [employer: string | { id: string }, contactPerson: string | { id: string } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\ContactPersonController::update
* @see app/Http/Controllers/ContactPersonController.php:30
* @route '/employers/{employer}/contact-persons/{contactPerson}'
*/
const updateForm = (args: { employer: string | { id: string }, contactPerson: string | { id: string } } | [employer: string | { id: string }, contactPerson: string | { id: string } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ContactPersonController::update
* @see app/Http/Controllers/ContactPersonController.php:30
* @route '/employers/{employer}/contact-persons/{contactPerson}'
*/
updateForm.put = (args: { employer: string | { id: string }, contactPerson: string | { id: string } } | [employer: string | { id: string }, contactPerson: string | { id: string } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\ContactPersonController::destroy
* @see app/Http/Controllers/ContactPersonController.php:48
* @route '/employers/{employer}/contact-persons/{contactPerson}'
*/
export const destroy = (args: { employer: string | { id: string }, contactPerson: string | { id: string } } | [employer: string | { id: string }, contactPerson: string | { id: string } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/employers/{employer}/contact-persons/{contactPerson}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\ContactPersonController::destroy
* @see app/Http/Controllers/ContactPersonController.php:48
* @route '/employers/{employer}/contact-persons/{contactPerson}'
*/
destroy.url = (args: { employer: string | { id: string }, contactPerson: string | { id: string } } | [employer: string | { id: string }, contactPerson: string | { id: string } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            employer: args[0],
            contactPerson: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        employer: typeof args.employer === 'object'
        ? args.employer.id
        : args.employer,
        contactPerson: typeof args.contactPerson === 'object'
        ? args.contactPerson.id
        : args.contactPerson,
    }

    return destroy.definition.url
            .replace('{employer}', parsedArgs.employer.toString())
            .replace('{contactPerson}', parsedArgs.contactPerson.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContactPersonController::destroy
* @see app/Http/Controllers/ContactPersonController.php:48
* @route '/employers/{employer}/contact-persons/{contactPerson}'
*/
destroy.delete = (args: { employer: string | { id: string }, contactPerson: string | { id: string } } | [employer: string | { id: string }, contactPerson: string | { id: string } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\ContactPersonController::destroy
* @see app/Http/Controllers/ContactPersonController.php:48
* @route '/employers/{employer}/contact-persons/{contactPerson}'
*/
const destroyForm = (args: { employer: string | { id: string }, contactPerson: string | { id: string } } | [employer: string | { id: string }, contactPerson: string | { id: string } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ContactPersonController::destroy
* @see app/Http/Controllers/ContactPersonController.php:48
* @route '/employers/{employer}/contact-persons/{contactPerson}'
*/
destroyForm.delete = (args: { employer: string | { id: string }, contactPerson: string | { id: string } } | [employer: string | { id: string }, contactPerson: string | { id: string } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const contactPersons = {
    store: Object.assign(store, store),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
}

export default contactPersons