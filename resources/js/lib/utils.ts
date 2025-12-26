import { InertiaLinkProps } from '@inertiajs/vue3';
import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function urlIsActive(
    urlToCheck: NonNullable<InertiaLinkProps['href']>,
    currentUrl: string,
) {
    const baseUrl = toUrl(urlToCheck);
    // Remove query string from current URL for comparison
    const currentPath = currentUrl.split('?')[0];
    // Check if current path starts with the nav item's base URL
    // For root paths like "/dashboard", we need exact match or with trailing content
    if (baseUrl === '/') {
        return currentPath === '/';
    }
    return currentPath === baseUrl || currentPath.startsWith(baseUrl + '/');
}

export function toUrl(href: NonNullable<InertiaLinkProps['href']>) {
    return typeof href === 'string' ? href : href?.url;
}
