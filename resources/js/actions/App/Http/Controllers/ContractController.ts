import {
    queryParams,
    type RouteQueryOptions,
    type RouteDefinition,
    type RouteFormDefinition,
    applyUrlDefaults,
} from './../../../../wayfinder';
/**
 * @see \App\Http\Controllers\ContractController::store
 * @see app/Http/Controllers/ContractController.php:18
 * @route '/employers/{employer}/contracts'
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
    url: '/employers/{employer}/contracts',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\ContractController::store
 * @see app/Http/Controllers/ContractController.php:18
 * @route '/employers/{employer}/contracts'
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
 * @see \App\Http\Controllers\ContractController::store
 * @see app/Http/Controllers/ContractController.php:18
 * @route '/employers/{employer}/contracts'
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
 * @see \App\Http\Controllers\ContractController::store
 * @see app/Http/Controllers/ContractController.php:18
 * @route '/employers/{employer}/contracts'
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
 * @see \App\Http\Controllers\ContractController::store
 * @see app/Http/Controllers/ContractController.php:18
 * @route '/employers/{employer}/contracts'
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

const ContractController = { store };

export default ContractController;
