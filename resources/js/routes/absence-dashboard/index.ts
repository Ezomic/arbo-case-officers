import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'

/**
* @see \App\Http\Controllers\AbsenceDashboardController::index
* @route '/dashboard'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ['get', 'head'],
    url: '/dashboard',
} satisfies RouteDefinition<['get', 'head']>

index.url = (options?: RouteQueryOptions) => index.definition.url + queryParams(options)
index.get = index

const dashboard = { index }
export default dashboard
