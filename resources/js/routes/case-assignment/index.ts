import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'

type CaseArg = { case: string | { id: string } } | string | { id: string }

function resolveCaseId(args: CaseArg): string {
    if (typeof args === 'string') return args
    if ('id' in args && !('case' in args)) return (args as { id: string }).id
    const obj = args as { case: string | { id: string } }
    return typeof obj.case === 'string' ? obj.case : obj.case.id
}

/**
* @see \App\Http\Controllers\CaseAssignmentController::update
* @route '/cases/{case}/assignment'
*/
export const update = (args: CaseArg, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ['put'],
    url: '/cases/{case}/assignment',
} satisfies RouteDefinition<['put']>

update.url = (args: CaseArg, options?: RouteQueryOptions) =>
    update.definition.url.replace('{case}', resolveCaseId(applyUrlDefaults(args as object) as CaseArg)).replace(/\/+$/, '') + queryParams(options)

update.put = update

const updateForm = (args: CaseArg, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, { [options?.mergeQuery ? 'mergeQuery' : 'query']: { _method: 'PUT', ...(options?.query ?? options?.mergeQuery ?? {}) } }),
    method: 'post',
})
updateForm.put = updateForm
update.form = updateForm

const caseAssignment = { update }
export default caseAssignment
