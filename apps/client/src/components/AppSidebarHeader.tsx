import * as React from 'react';
import { Breadcrumbs } from '@/components/Breadcrumbs';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

export const AppSidebarHeader: React.FC<Props> = ({ breadcrumbs = [] }) => {
    return (
        <header
            className="flex h-16 shrink-0 items-center gap-2 border-b border-border/40 bg-background/80 backdrop-blur-md sticky top-0 z-10 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
        >
            <div className="flex items-center gap-2">
                <SidebarTrigger className="-ml-1 rounded-xl hover:bg-muted" />
                {breadcrumbs.length > 0 && (
                    <div className="flex items-center gap-2 ml-2">
                        <Breadcrumbs breadcrumbs={breadcrumbs} />
                    </div>
                )}
            </div>
        </header>
    );
};
