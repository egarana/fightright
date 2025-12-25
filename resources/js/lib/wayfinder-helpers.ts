type InertiaMethod = 'GET' | 'POST' | 'PUT' | 'DELETE' | 'PATCH';

export type InertiaFormDefinition = {
    action: string;
    method: InertiaMethod;
};

/**
 * Creates a form definition for use with Inertia's Form component.
 * Converts lowercase method to uppercase as required by Inertia.
 */
export function createFormAction(
    url: string,
    method: 'get' | 'post' | 'put' | 'delete' | 'patch' = 'post'
): InertiaFormDefinition {
    return {
        action: url,
        method: method.toUpperCase() as InertiaMethod,
    };
}
