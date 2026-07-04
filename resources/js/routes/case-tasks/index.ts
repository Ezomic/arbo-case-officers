import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'

type CaseArg = { case: string | { id: string } } | string | { id: string }

function resolveCaseArg(args: CaseArg): string {
    if (typeof args === 'string') return args
    if ('id' in args && !('case' in args)) return (args as { id: string }).id
    const obj = args as { case: string | { id: string } }
    return typeof obj.case === 'string' ? obj.case : obj.case.id
}

type CaseTaskArgs = { case: string | { id: string }; task: string | { id: string } }

function resolveTaskArgs(args: CaseTaskArgs): { caseId: string; taskId: string } {
    const caseId = typeof args.case === 'string' ? args.case : args.case.id
    const taskId = typeof args.task === 'string' ? args.task : args.task.id
    return { caseId, taskId }
}

/**
* @see \App\Http\Controllers\CaseTaskController::store
* @route '/cases/{case}/tasks'
*/
export const store = (args: CaseArg, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})
store.definition = { methods: ['post'], url: '/cases/{case}/tasks' } satisfies RouteDefinition<['post']>
store.url = (args: CaseArg, options?: RouteQueryOptions) =>
    store.definition.url.replace('{case}', resolveCaseArg(applyUrlDefaults(args as object) as CaseArg)).replace(/\/+$/, '') + queryParams(options)
store.post = store
const storeForm = (args: CaseArg, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({ action: store.url(args, options), method: 'post' })
storeForm.post = storeForm
store.form = storeForm

/**
* @see \App\Http\Controllers\CaseTaskController::update
* @route '/cases/{case}/tasks/{task}'
*/
export const update = (args: CaseTaskArgs, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})
update.definition = { methods: ['put'], url: '/cases/{case}/tasks/{task}' } satisfies RouteDefinition<['put']>
update.url = (args: CaseTaskArgs, options?: RouteQueryOptions) => {
    const { caseId, taskId } = resolveTaskArgs(args)
    return update.definition.url.replace('{case}', caseId).replace('{task}', taskId).replace(/\/+$/, '') + queryParams(options)
}
update.put = update
const updateForm = (args: CaseTaskArgs, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, { [options?.mergeQuery ? 'mergeQuery' : 'query']: { _method: 'PUT', ...(options?.query ?? options?.mergeQuery ?? {}) } }),
    method: 'post',
})
updateForm.put = updateForm
update.form = updateForm

/**
* @see \App\Http\Controllers\CaseTaskController::complete
* @route '/cases/{case}/tasks/{task}/complete'
*/
export const complete = (args: CaseTaskArgs, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: complete.url(args, options),
    method: 'post',
})
complete.definition = { methods: ['post'], url: '/cases/{case}/tasks/{task}/complete' } satisfies RouteDefinition<['post']>
complete.url = (args: CaseTaskArgs, options?: RouteQueryOptions) => {
    const { caseId, taskId } = resolveTaskArgs(args)
    return complete.definition.url.replace('{case}', caseId).replace('{task}', taskId).replace(/\/+$/, '') + queryParams(options)
}
complete.post = complete
const completeForm = (args: CaseTaskArgs, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({ action: complete.url(args, options), method: 'post' })
completeForm.post = completeForm
complete.form = completeForm

/**
* @see \App\Http\Controllers\CaseTaskController::destroy
* @route '/cases/{case}/tasks/{task}'
*/
export const destroy = (args: CaseTaskArgs, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})
destroy.definition = { methods: ['delete'], url: '/cases/{case}/tasks/{task}' } satisfies RouteDefinition<['delete']>
destroy.url = (args: CaseTaskArgs, options?: RouteQueryOptions) => {
    const { caseId, taskId } = resolveTaskArgs(args)
    return destroy.definition.url.replace('{case}', caseId).replace('{task}', taskId).replace(/\/+$/, '') + queryParams(options)
}
destroy.delete = destroy
const destroyForm = (args: CaseTaskArgs, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, { [options?.mergeQuery ? 'mergeQuery' : 'query']: { _method: 'DELETE', ...(options?.query ?? options?.mergeQuery ?? {}) } }),
    method: 'post',
})
destroyForm.delete = destroyForm
destroy.form = destroyForm

const caseTasks = { store, update, complete, destroy }
export default caseTasks
