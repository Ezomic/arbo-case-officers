import {
    queryParams,
    type RouteQueryOptions,
    type RouteDefinition,
    type RouteFormDefinition,
    applyUrlDefaults,
} from './../../../../wayfinder';
/**
 * @see \App\Http\Controllers\GdprExportController::employee
 * @see app/Http/Controllers/GdprExportController.php:14
 * @route '/employees/{employee}/gdpr-export'
 */
export const employee = (
    args:
        | { employee: string | { id: string } }
        | [employee: string | { id: string }]
        | string
        | { id: string },
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: employee.url(args, options),
    method: 'get',
});

employee.definition = {
    methods: ['get', 'head'],
    url: '/employees/{employee}/gdpr-export',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\GdprExportController::employee
 * @see app/Http/Controllers/GdprExportController.php:14
 * @route '/employees/{employee}/gdpr-export'
 */
employee.url = (
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
        employee.definition.url
            .replace('{employee}', parsedArgs.employee.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\GdprExportController::employee
 * @see app/Http/Controllers/GdprExportController.php:14
 * @route '/employees/{employee}/gdpr-export'
 */
employee.get = (
    args:
        | { employee: string | { id: string } }
        | [employee: string | { id: string }]
        | string
        | { id: string },
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: employee.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\GdprExportController::employee
 * @see app/Http/Controllers/GdprExportController.php:14
 * @route '/employees/{employee}/gdpr-export'
 */
employee.head = (
    args:
        | { employee: string | { id: string } }
        | [employee: string | { id: string }]
        | string
        | { id: string },
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: employee.url(args, options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\GdprExportController::employee
 * @see app/Http/Controllers/GdprExportController.php:14
 * @route '/employees/{employee}/gdpr-export'
 */
const employeeForm = (
    args:
        | { employee: string | { id: string } }
        | [employee: string | { id: string }]
        | string
        | { id: string },
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: employee.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\GdprExportController::employee
 * @see app/Http/Controllers/GdprExportController.php:14
 * @route '/employees/{employee}/gdpr-export'
 */
employeeForm.get = (
    args:
        | { employee: string | { id: string } }
        | [employee: string | { id: string }]
        | string
        | { id: string },
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: employee.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\GdprExportController::employee
 * @see app/Http/Controllers/GdprExportController.php:14
 * @route '/employees/{employee}/gdpr-export'
 */
employeeForm.head = (
    args:
        | { employee: string | { id: string } }
        | [employee: string | { id: string }]
        | string
        | { id: string },
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: employee.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

employee.form = employeeForm;

const GdprExportController = { employee };

export default GdprExportController;
