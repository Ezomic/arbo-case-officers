import {
    queryParams,
    type RouteQueryOptions,
    type RouteDefinition,
    type RouteFormDefinition,
    applyUrlDefaults,
} from './../../../../wayfinder';
/**
 * @see \App\Http\Controllers\CaseAssignmentController::update
 * @see app/Http/Controllers/CaseAssignmentController.php:15
 * @route '/cases/{case}/assignment'
 */
export const update = (
    args:
        | { case: string | { id: string } }
        | [caseParam: string | { id: string }]
        | string
        | { id: string },
    options?: RouteQueryOptions,
): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
});

update.definition = {
    methods: ['put'],
    url: '/cases/{case}/assignment',
} satisfies RouteDefinition<['put']>;

/**
 * @see \App\Http\Controllers\CaseAssignmentController::update
 * @see app/Http/Controllers/CaseAssignmentController.php:15
 * @route '/cases/{case}/assignment'
 */
update.url = (
    args:
        | { case: string | { id: string } }
        | [caseParam: string | { id: string }]
        | string
        | { id: string },
    options?: RouteQueryOptions,
) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { case: args };
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { case: args.id };
    }

    if (Array.isArray(args)) {
        args = {
            case: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        case: typeof args.case === 'object' ? args.case.id : args.case,
    };

    return (
        update.definition.url
            .replace('{case}', parsedArgs.case.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\CaseAssignmentController::update
 * @see app/Http/Controllers/CaseAssignmentController.php:15
 * @route '/cases/{case}/assignment'
 */
update.put = (
    args:
        | { case: string | { id: string } }
        | [caseParam: string | { id: string }]
        | string
        | { id: string },
    options?: RouteQueryOptions,
): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
});

/**
 * @see \App\Http\Controllers\CaseAssignmentController::update
 * @see app/Http/Controllers/CaseAssignmentController.php:15
 * @route '/cases/{case}/assignment'
 */
const updateForm = (
    args:
        | { case: string | { id: string } }
        | [caseParam: string | { id: string }]
        | string
        | { id: string },
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
 * @see \App\Http\Controllers\CaseAssignmentController::update
 * @see app/Http/Controllers/CaseAssignmentController.php:15
 * @route '/cases/{case}/assignment'
 */
updateForm.put = (
    args:
        | { case: string | { id: string } }
        | [caseParam: string | { id: string }]
        | string
        | { id: string },
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

const CaseAssignmentController = { update };

export default CaseAssignmentController;
