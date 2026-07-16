import {
    queryParams,
    type RouteQueryOptions,
    type RouteDefinition,
    type RouteFormDefinition,
    applyUrlDefaults,
} from './../../wayfinder';
/**
 * @see \App\Http\Controllers\EmployeeController::index
 * @see app/Http/Controllers/EmployeeController.php:19
 * @route '/employees'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
});

index.definition = {
    methods: ['get', 'head'],
    url: '/employees',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\EmployeeController::index
 * @see app/Http/Controllers/EmployeeController.php:19
 * @route '/employees'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\EmployeeController::index
 * @see app/Http/Controllers/EmployeeController.php:19
 * @route '/employees'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\EmployeeController::index
 * @see app/Http/Controllers/EmployeeController.php:19
 * @route '/employees'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\EmployeeController::index
 * @see app/Http/Controllers/EmployeeController.php:19
 * @route '/employees'
 */
const indexForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\EmployeeController::index
 * @see app/Http/Controllers/EmployeeController.php:19
 * @route '/employees'
 */
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\EmployeeController::index
 * @see app/Http/Controllers/EmployeeController.php:19
 * @route '/employees'
 */
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

index.form = indexForm;

/**
 * @see \App\Http\Controllers\EmployeeController::search
 * @see app/Http/Controllers/EmployeeController.php:31
 * @route '/employees/search'
 */
export const search = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: search.url(options),
    method: 'get',
});

search.definition = {
    methods: ['get', 'head'],
    url: '/employees/search',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\EmployeeController::search
 * @see app/Http/Controllers/EmployeeController.php:31
 * @route '/employees/search'
 */
search.url = (options?: RouteQueryOptions) => {
    return search.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\EmployeeController::search
 * @see app/Http/Controllers/EmployeeController.php:31
 * @route '/employees/search'
 */
search.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: search.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\EmployeeController::search
 * @see app/Http/Controllers/EmployeeController.php:31
 * @route '/employees/search'
 */
search.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: search.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\EmployeeController::search
 * @see app/Http/Controllers/EmployeeController.php:31
 * @route '/employees/search'
 */
const searchForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: search.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\EmployeeController::search
 * @see app/Http/Controllers/EmployeeController.php:31
 * @route '/employees/search'
 */
searchForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: search.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\EmployeeController::search
 * @see app/Http/Controllers/EmployeeController.php:31
 * @route '/employees/search'
 */
searchForm.head = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: search.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

search.form = searchForm;

/**
 * @see \App\Http\Controllers\GdprExportController::gdprExport
 * @see app/Http/Controllers/GdprExportController.php:14
 * @route '/employees/{employee}/gdpr-export'
 */
export const gdprExport = (
    args:
        | { employee: string | { id: string } }
        | [employee: string | { id: string }]
        | string
        | { id: string },
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: gdprExport.url(args, options),
    method: 'get',
});

gdprExport.definition = {
    methods: ['get', 'head'],
    url: '/employees/{employee}/gdpr-export',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\GdprExportController::gdprExport
 * @see app/Http/Controllers/GdprExportController.php:14
 * @route '/employees/{employee}/gdpr-export'
 */
gdprExport.url = (
    args:
        | { employee: string | { id: string } }
        | [employee: string | { id: string }]
        | string
        | { id: string },
    options?: RouteQueryOptions,
) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { employee: args };
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { employee: args.id };
    }

    if (Array.isArray(args)) {
        args = {
            employee: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        employee:
            typeof args.employee === 'object'
                ? args.employee.id
                : args.employee,
    };

    return (
        gdprExport.definition.url
            .replace('{employee}', parsedArgs.employee.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\GdprExportController::gdprExport
 * @see app/Http/Controllers/GdprExportController.php:14
 * @route '/employees/{employee}/gdpr-export'
 */
gdprExport.get = (
    args:
        | { employee: string | { id: string } }
        | [employee: string | { id: string }]
        | string
        | { id: string },
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: gdprExport.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\GdprExportController::gdprExport
 * @see app/Http/Controllers/GdprExportController.php:14
 * @route '/employees/{employee}/gdpr-export'
 */
gdprExport.head = (
    args:
        | { employee: string | { id: string } }
        | [employee: string | { id: string }]
        | string
        | { id: string },
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: gdprExport.url(args, options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\GdprExportController::gdprExport
 * @see app/Http/Controllers/GdprExportController.php:14
 * @route '/employees/{employee}/gdpr-export'
 */
const gdprExportForm = (
    args:
        | { employee: string | { id: string } }
        | [employee: string | { id: string }]
        | string
        | { id: string },
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: gdprExport.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\GdprExportController::gdprExport
 * @see app/Http/Controllers/GdprExportController.php:14
 * @route '/employees/{employee}/gdpr-export'
 */
gdprExportForm.get = (
    args:
        | { employee: string | { id: string } }
        | [employee: string | { id: string }]
        | string
        | { id: string },
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: gdprExport.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\GdprExportController::gdprExport
 * @see app/Http/Controllers/GdprExportController.php:14
 * @route '/employees/{employee}/gdpr-export'
 */
gdprExportForm.head = (
    args:
        | { employee: string | { id: string } }
        | [employee: string | { id: string }]
        | string
        | { id: string },
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: gdprExport.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

gdprExport.form = gdprExportForm;

/**
 * @see \App\Http\Controllers\EmployeeController::store
 * @see app/Http/Controllers/EmployeeController.php:90
 * @route '/employers/{employer}/employees'
 */
export const store = (
    args:
        | { employer: string | { id: string } }
        | [employer: string | { id: string }]
        | string
        | { id: string },
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
});

store.definition = {
    methods: ['post'],
    url: '/employers/{employer}/employees',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\EmployeeController::store
 * @see app/Http/Controllers/EmployeeController.php:90
 * @route '/employers/{employer}/employees'
 */
store.url = (
    args:
        | { employer: string | { id: string } }
        | [employer: string | { id: string }]
        | string
        | { id: string },
    options?: RouteQueryOptions,
) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { employer: args };
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { employer: args.id };
    }

    if (Array.isArray(args)) {
        args = {
            employer: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        employer:
            typeof args.employer === 'object'
                ? args.employer.id
                : args.employer,
    };

    return (
        store.definition.url
            .replace('{employer}', parsedArgs.employer.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\EmployeeController::store
 * @see app/Http/Controllers/EmployeeController.php:90
 * @route '/employers/{employer}/employees'
 */
store.post = (
    args:
        | { employer: string | { id: string } }
        | [employer: string | { id: string }]
        | string
        | { id: string },
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\EmployeeController::store
 * @see app/Http/Controllers/EmployeeController.php:90
 * @route '/employers/{employer}/employees'
 */
const storeForm = (
    args:
        | { employer: string | { id: string } }
        | [employer: string | { id: string }]
        | string
        | { id: string },
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\EmployeeController::store
 * @see app/Http/Controllers/EmployeeController.php:90
 * @route '/employers/{employer}/employees'
 */
storeForm.post = (
    args:
        | { employer: string | { id: string } }
        | [employer: string | { id: string }]
        | string
        | { id: string },
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
});

store.form = storeForm;

/**
 * @see \App\Http\Controllers\EmployeeController::edit
 * @see app/Http/Controllers/EmployeeController.php:116
 * @route '/employers/{employer}/employees/{employee}/edit'
 */
export const edit = (
    args:
        | {
              employer: string | { id: string };
              employee: string | { id: string };
          }
        | [
              employer: string | { id: string },
              employee: string | { id: string },
          ],
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
});

edit.definition = {
    methods: ['get', 'head'],
    url: '/employers/{employer}/employees/{employee}/edit',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\EmployeeController::edit
 * @see app/Http/Controllers/EmployeeController.php:116
 * @route '/employers/{employer}/employees/{employee}/edit'
 */
edit.url = (
    args:
        | {
              employer: string | { id: string };
              employee: string | { id: string };
          }
        | [
              employer: string | { id: string },
              employee: string | { id: string },
          ],
    options?: RouteQueryOptions,
) => {
    if (Array.isArray(args)) {
        args = {
            employer: args[0],
            employee: args[1],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        employer:
            typeof args.employer === 'object'
                ? args.employer.id
                : args.employer,
        employee:
            typeof args.employee === 'object'
                ? args.employee.id
                : args.employee,
    };

    return (
        edit.definition.url
            .replace('{employer}', parsedArgs.employer.toString())
            .replace('{employee}', parsedArgs.employee.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\EmployeeController::edit
 * @see app/Http/Controllers/EmployeeController.php:116
 * @route '/employers/{employer}/employees/{employee}/edit'
 */
edit.get = (
    args:
        | {
              employer: string | { id: string };
              employee: string | { id: string };
          }
        | [
              employer: string | { id: string },
              employee: string | { id: string },
          ],
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\EmployeeController::edit
 * @see app/Http/Controllers/EmployeeController.php:116
 * @route '/employers/{employer}/employees/{employee}/edit'
 */
edit.head = (
    args:
        | {
              employer: string | { id: string };
              employee: string | { id: string };
          }
        | [
              employer: string | { id: string },
              employee: string | { id: string },
          ],
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\EmployeeController::edit
 * @see app/Http/Controllers/EmployeeController.php:116
 * @route '/employers/{employer}/employees/{employee}/edit'
 */
const editForm = (
    args:
        | {
              employer: string | { id: string };
              employee: string | { id: string };
          }
        | [
              employer: string | { id: string },
              employee: string | { id: string },
          ],
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\EmployeeController::edit
 * @see app/Http/Controllers/EmployeeController.php:116
 * @route '/employers/{employer}/employees/{employee}/edit'
 */
editForm.get = (
    args:
        | {
              employer: string | { id: string };
              employee: string | { id: string };
          }
        | [
              employer: string | { id: string },
              employee: string | { id: string },
          ],
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\EmployeeController::edit
 * @see app/Http/Controllers/EmployeeController.php:116
 * @route '/employers/{employer}/employees/{employee}/edit'
 */
editForm.head = (
    args:
        | {
              employer: string | { id: string };
              employee: string | { id: string };
          }
        | [
              employer: string | { id: string },
              employee: string | { id: string },
          ],
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: edit.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

edit.form = editForm;

/**
 * @see \App\Http\Controllers\EmployeeController::update
 * @see app/Http/Controllers/EmployeeController.php:129
 * @route '/employers/{employer}/employees/{employee}'
 */
export const update = (
    args:
        | {
              employer: string | { id: string };
              employee: string | { id: string };
          }
        | [
              employer: string | { id: string },
              employee: string | { id: string },
          ],
    options?: RouteQueryOptions,
): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
});

update.definition = {
    methods: ['put'],
    url: '/employers/{employer}/employees/{employee}',
} satisfies RouteDefinition<['put']>;

/**
 * @see \App\Http\Controllers\EmployeeController::update
 * @see app/Http/Controllers/EmployeeController.php:129
 * @route '/employers/{employer}/employees/{employee}'
 */
update.url = (
    args:
        | {
              employer: string | { id: string };
              employee: string | { id: string };
          }
        | [
              employer: string | { id: string },
              employee: string | { id: string },
          ],
    options?: RouteQueryOptions,
) => {
    if (Array.isArray(args)) {
        args = {
            employer: args[0],
            employee: args[1],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        employer:
            typeof args.employer === 'object'
                ? args.employer.id
                : args.employer,
        employee:
            typeof args.employee === 'object'
                ? args.employee.id
                : args.employee,
    };

    return (
        update.definition.url
            .replace('{employer}', parsedArgs.employer.toString())
            .replace('{employee}', parsedArgs.employee.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\EmployeeController::update
 * @see app/Http/Controllers/EmployeeController.php:129
 * @route '/employers/{employer}/employees/{employee}'
 */
update.put = (
    args:
        | {
              employer: string | { id: string };
              employee: string | { id: string };
          }
        | [
              employer: string | { id: string },
              employee: string | { id: string },
          ],
    options?: RouteQueryOptions,
): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
});

/**
 * @see \App\Http\Controllers\EmployeeController::update
 * @see app/Http/Controllers/EmployeeController.php:129
 * @route '/employers/{employer}/employees/{employee}'
 */
const updateForm = (
    args:
        | {
              employer: string | { id: string };
              employee: string | { id: string };
          }
        | [
              employer: string | { id: string },
              employee: string | { id: string },
          ],
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\EmployeeController::update
 * @see app/Http/Controllers/EmployeeController.php:129
 * @route '/employers/{employer}/employees/{employee}'
 */
updateForm.put = (
    args:
        | {
              employer: string | { id: string };
              employee: string | { id: string };
          }
        | [
              employer: string | { id: string },
              employee: string | { id: string },
          ],
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'post',
});

update.form = updateForm;

const employees = {
    index: Object.assign(index, index),
    search: Object.assign(search, search),
    gdprExport: Object.assign(gdprExport, gdprExport),
    store: Object.assign(store, store),
    edit: Object.assign(edit, edit),
    update: Object.assign(update, update),
};

export default employees;
