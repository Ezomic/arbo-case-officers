import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\OrganizationalUnitController::store
* @see app/Http/Controllers/OrganizationalUnitController.php:15
* @route '/employers/{employer}/organizational-units'
*/
export const store = (args: { employer: string | { id: string } } | [employer: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/employers/{employer}/organizational-units',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\OrganizationalUnitController::store
* @see app/Http/Controllers/OrganizationalUnitController.php:15
* @route '/employers/{employer}/organizational-units'
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
* @see \App\Http\Controllers\OrganizationalUnitController::store
* @see app/Http/Controllers/OrganizationalUnitController.php:15
* @route '/employers/{employer}/organizational-units'
*/
store.post = (args: { employer: string | { id: string } } | [employer: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\OrganizationalUnitController::store
* @see app/Http/Controllers/OrganizationalUnitController.php:15
* @route '/employers/{employer}/organizational-units'
*/
const storeForm = (args: { employer: string | { id: string } } | [employer: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\OrganizationalUnitController::store
* @see app/Http/Controllers/OrganizationalUnitController.php:15
* @route '/employers/{employer}/organizational-units'
*/
storeForm.post = (args: { employer: string | { id: string } } | [employer: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\OrganizationalUnitController::edit
* @see app/Http/Controllers/OrganizationalUnitController.php:32
* @route '/employers/{employer}/organizational-units/{organizationalUnit}/edit'
*/
export const edit = (args: { employer: string | { id: string }, organizationalUnit: string | { id: string } } | [employer: string | { id: string }, organizationalUnit: string | { id: string } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/employers/{employer}/organizational-units/{organizationalUnit}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\OrganizationalUnitController::edit
* @see app/Http/Controllers/OrganizationalUnitController.php:32
* @route '/employers/{employer}/organizational-units/{organizationalUnit}/edit'
*/
edit.url = (args: { employer: string | { id: string }, organizationalUnit: string | { id: string } } | [employer: string | { id: string }, organizationalUnit: string | { id: string } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            employer: args[0],
            organizationalUnit: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        employer: typeof args.employer === 'object'
        ? args.employer.id
        : args.employer,
        organizationalUnit: typeof args.organizationalUnit === 'object'
        ? args.organizationalUnit.id
        : args.organizationalUnit,
    }

    return edit.definition.url
            .replace('{employer}', parsedArgs.employer.toString())
            .replace('{organizationalUnit}', parsedArgs.organizationalUnit.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\OrganizationalUnitController::edit
* @see app/Http/Controllers/OrganizationalUnitController.php:32
* @route '/employers/{employer}/organizational-units/{organizationalUnit}/edit'
*/
edit.get = (args: { employer: string | { id: string }, organizationalUnit: string | { id: string } } | [employer: string | { id: string }, organizationalUnit: string | { id: string } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OrganizationalUnitController::edit
* @see app/Http/Controllers/OrganizationalUnitController.php:32
* @route '/employers/{employer}/organizational-units/{organizationalUnit}/edit'
*/
edit.head = (args: { employer: string | { id: string }, organizationalUnit: string | { id: string } } | [employer: string | { id: string }, organizationalUnit: string | { id: string } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\OrganizationalUnitController::edit
* @see app/Http/Controllers/OrganizationalUnitController.php:32
* @route '/employers/{employer}/organizational-units/{organizationalUnit}/edit'
*/
const editForm = (args: { employer: string | { id: string }, organizationalUnit: string | { id: string } } | [employer: string | { id: string }, organizationalUnit: string | { id: string } ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OrganizationalUnitController::edit
* @see app/Http/Controllers/OrganizationalUnitController.php:32
* @route '/employers/{employer}/organizational-units/{organizationalUnit}/edit'
*/
editForm.get = (args: { employer: string | { id: string }, organizationalUnit: string | { id: string } } | [employer: string | { id: string }, organizationalUnit: string | { id: string } ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OrganizationalUnitController::edit
* @see app/Http/Controllers/OrganizationalUnitController.php:32
* @route '/employers/{employer}/organizational-units/{organizationalUnit}/edit'
*/
editForm.head = (args: { employer: string | { id: string }, organizationalUnit: string | { id: string } } | [employer: string | { id: string }, organizationalUnit: string | { id: string } ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

edit.form = editForm

/**
* @see \App\Http\Controllers\OrganizationalUnitController::update
* @see app/Http/Controllers/OrganizationalUnitController.php:46
* @route '/employers/{employer}/organizational-units/{organizationalUnit}'
*/
export const update = (args: { employer: string | { id: string }, organizationalUnit: string | { id: string } } | [employer: string | { id: string }, organizationalUnit: string | { id: string } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/employers/{employer}/organizational-units/{organizationalUnit}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\OrganizationalUnitController::update
* @see app/Http/Controllers/OrganizationalUnitController.php:46
* @route '/employers/{employer}/organizational-units/{organizationalUnit}'
*/
update.url = (args: { employer: string | { id: string }, organizationalUnit: string | { id: string } } | [employer: string | { id: string }, organizationalUnit: string | { id: string } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            employer: args[0],
            organizationalUnit: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        employer: typeof args.employer === 'object'
        ? args.employer.id
        : args.employer,
        organizationalUnit: typeof args.organizationalUnit === 'object'
        ? args.organizationalUnit.id
        : args.organizationalUnit,
    }

    return update.definition.url
            .replace('{employer}', parsedArgs.employer.toString())
            .replace('{organizationalUnit}', parsedArgs.organizationalUnit.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\OrganizationalUnitController::update
* @see app/Http/Controllers/OrganizationalUnitController.php:46
* @route '/employers/{employer}/organizational-units/{organizationalUnit}'
*/
update.put = (args: { employer: string | { id: string }, organizationalUnit: string | { id: string } } | [employer: string | { id: string }, organizationalUnit: string | { id: string } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\OrganizationalUnitController::update
* @see app/Http/Controllers/OrganizationalUnitController.php:46
* @route '/employers/{employer}/organizational-units/{organizationalUnit}'
*/
const updateForm = (args: { employer: string | { id: string }, organizationalUnit: string | { id: string } } | [employer: string | { id: string }, organizationalUnit: string | { id: string } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\OrganizationalUnitController::update
* @see app/Http/Controllers/OrganizationalUnitController.php:46
* @route '/employers/{employer}/organizational-units/{organizationalUnit}'
*/
updateForm.put = (args: { employer: string | { id: string }, organizationalUnit: string | { id: string } } | [employer: string | { id: string }, organizationalUnit: string | { id: string } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

const OrganizationalUnitController = { store, edit, update }

export default OrganizationalUnitController