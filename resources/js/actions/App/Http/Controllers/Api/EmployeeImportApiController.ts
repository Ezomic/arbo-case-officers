import {
    queryParams,
    type RouteQueryOptions,
    type RouteDefinition,
    type RouteFormDefinition,
    applyUrlDefaults,
} from './../../../../../wayfinder';
/**
 * @see \App\Http\Controllers\Api\EmployeeImportApiController::store
 * @see app/Http/Controllers/Api/EmployeeImportApiController.php:20
 * @route '/api/employers/{employer}/employees/import'
 */
export const store = (
    args:
        | { employer: string | number }
        | [employer: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
});

store.definition = {
    methods: ['post'],
    url: '/api/employers/{employer}/employees/import',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\Api\EmployeeImportApiController::store
 * @see app/Http/Controllers/Api/EmployeeImportApiController.php:20
 * @route '/api/employers/{employer}/employees/import'
 */
store.url = (
    args:
        | { employer: string | number }
        | [employer: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { employer: args };
    }

    if (Array.isArray(args)) {
        args = {
            employer: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        employer: args.employer,
    };

    return (
        store.definition.url
            .replace('{employer}', parsedArgs.employer.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\EmployeeImportApiController::store
 * @see app/Http/Controllers/Api/EmployeeImportApiController.php:20
 * @route '/api/employers/{employer}/employees/import'
 */
store.post = (
    args:
        | { employer: string | number }
        | [employer: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\EmployeeImportApiController::store
 * @see app/Http/Controllers/Api/EmployeeImportApiController.php:20
 * @route '/api/employers/{employer}/employees/import'
 */
const storeForm = (
    args:
        | { employer: string | number }
        | [employer: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\EmployeeImportApiController::store
 * @see app/Http/Controllers/Api/EmployeeImportApiController.php:20
 * @route '/api/employers/{employer}/employees/import'
 */
storeForm.post = (
    args:
        | { employer: string | number }
        | [employer: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
});

store.form = storeForm;

/**
 * @see \App\Http\Controllers\Api\EmployeeImportApiController::show
 * @see app/Http/Controllers/Api/EmployeeImportApiController.php:49
 * @route '/api/employee-imports/{employeeImport}'
 */
export const show = (
    args:
        | { employeeImport: number | { id: number } }
        | [employeeImport: number | { id: number }]
        | number
        | { id: number },
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
});

show.definition = {
    methods: ['get', 'head'],
    url: '/api/employee-imports/{employeeImport}',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\EmployeeImportApiController::show
 * @see app/Http/Controllers/Api/EmployeeImportApiController.php:49
 * @route '/api/employee-imports/{employeeImport}'
 */
show.url = (
    args:
        | { employeeImport: number | { id: number } }
        | [employeeImport: number | { id: number }]
        | number
        | { id: number },
    options?: RouteQueryOptions,
) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { employeeImport: args };
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { employeeImport: args.id };
    }

    if (Array.isArray(args)) {
        args = {
            employeeImport: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        employeeImport:
            typeof args.employeeImport === 'object'
                ? args.employeeImport.id
                : args.employeeImport,
    };

    return (
        show.definition.url
            .replace('{employeeImport}', parsedArgs.employeeImport.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\EmployeeImportApiController::show
 * @see app/Http/Controllers/Api/EmployeeImportApiController.php:49
 * @route '/api/employee-imports/{employeeImport}'
 */
show.get = (
    args:
        | { employeeImport: number | { id: number } }
        | [employeeImport: number | { id: number }]
        | number
        | { id: number },
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\EmployeeImportApiController::show
 * @see app/Http/Controllers/Api/EmployeeImportApiController.php:49
 * @route '/api/employee-imports/{employeeImport}'
 */
show.head = (
    args:
        | { employeeImport: number | { id: number } }
        | [employeeImport: number | { id: number }]
        | number
        | { id: number },
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\EmployeeImportApiController::show
 * @see app/Http/Controllers/Api/EmployeeImportApiController.php:49
 * @route '/api/employee-imports/{employeeImport}'
 */
const showForm = (
    args:
        | { employeeImport: number | { id: number } }
        | [employeeImport: number | { id: number }]
        | number
        | { id: number },
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\EmployeeImportApiController::show
 * @see app/Http/Controllers/Api/EmployeeImportApiController.php:49
 * @route '/api/employee-imports/{employeeImport}'
 */
showForm.get = (
    args:
        | { employeeImport: number | { id: number } }
        | [employeeImport: number | { id: number }]
        | number
        | { id: number },
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\EmployeeImportApiController::show
 * @see app/Http/Controllers/Api/EmployeeImportApiController.php:49
 * @route '/api/employee-imports/{employeeImport}'
 */
showForm.head = (
    args:
        | { employeeImport: number | { id: number } }
        | [employeeImport: number | { id: number }]
        | number
        | { id: number },
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

show.form = showForm;

const EmployeeImportApiController = { store, show };

export default EmployeeImportApiController;
