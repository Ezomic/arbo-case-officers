import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'

/**
* @see \App\Http\Controllers\CaseNoteController::store
* @route '/cases/{case}/notes'
*/
export const store = (args: { case: string | { id: string } } | [caseParam: string | { id: string }] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ['post'],
    url: '/cases/{case}/notes',
} satisfies RouteDefinition<['post']>

store.url = (args: { case: string | { id: string } } | [caseParam: string | { id: string }] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') args = { case: args }
    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) args = { case: args.id }
    if (Array.isArray(args)) args = { case: args[0] }
    args = applyUrlDefaults(args)
    const parsedArgs = { case: typeof args.case === 'object' ? args.case.id : args.case }
    return store.definition.url.replace('{case}', parsedArgs.case.toString()).replace(/\/+$/, '') + queryParams(options)
}

store.post = (args: { case: string | { id: string } } | [caseParam: string | { id: string }] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

const storeForm = (args: { case: string | { id: string } } | [caseParam: string | { id: string }] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})
storeForm.post = storeForm
store.form = storeForm

/**
* @see \App\Http\Controllers\CaseNoteController::update
* @route '/cases/{case}/notes/{note}'
*/
export const update = (args: { case: string | { id: string }, note: string | { id: string } }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ['put'],
    url: '/cases/{case}/notes/{note}',
} satisfies RouteDefinition<['put']>

update.url = (args: { case: string | { id: string }, note: string | { id: string } }, options?: RouteQueryOptions) => {
    args = applyUrlDefaults(args)
    const caseId = typeof args.case === 'object' ? args.case.id : args.case
    const noteId = typeof args.note === 'object' ? args.note.id : args.note
    return update.definition.url
        .replace('{case}', caseId.toString())
        .replace('{note}', noteId.toString())
        .replace(/\/+$/, '') + queryParams(options)
}

update.put = (args: { case: string | { id: string }, note: string | { id: string } }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

const updateForm = (args: { case: string | { id: string }, note: string | { id: string } }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, { [options?.mergeQuery ? 'mergeQuery' : 'query']: { _method: 'PUT', ...(options?.query ?? options?.mergeQuery ?? {}) } }),
    method: 'post',
})
updateForm.put = updateForm
update.form = updateForm

/**
* @see \App\Http\Controllers\CaseNoteController::destroy
* @route '/cases/{case}/notes/{note}'
*/
export const destroy = (args: { case: string | { id: string }, note: string | { id: string } }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ['delete'],
    url: '/cases/{case}/notes/{note}',
} satisfies RouteDefinition<['delete']>

destroy.url = (args: { case: string | { id: string }, note: string | { id: string } }, options?: RouteQueryOptions) => {
    args = applyUrlDefaults(args)
    const caseId = typeof args.case === 'object' ? args.case.id : args.case
    const noteId = typeof args.note === 'object' ? args.note.id : args.note
    return destroy.definition.url
        .replace('{case}', caseId.toString())
        .replace('{note}', noteId.toString())
        .replace(/\/+$/, '') + queryParams(options)
}

destroy.delete = (args: { case: string | { id: string }, note: string | { id: string } }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

const destroyForm = (args: { case: string | { id: string }, note: string | { id: string } }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, { [options?.mergeQuery ? 'mergeQuery' : 'query']: { _method: 'DELETE', ...(options?.query ?? options?.mergeQuery ?? {}) } }),
    method: 'post',
})
destroyForm.delete = destroyForm
destroy.form = destroyForm

const caseNotes = { store, update, destroy }

export default caseNotes
