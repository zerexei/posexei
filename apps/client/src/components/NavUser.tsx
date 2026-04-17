import * as React from 'react';
import { UserInfo } from '@/components/UserInfo';
import { SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { mockUser } from '@/mocks';
import { ChevronsUpDown } from 'lucide-react';

export const NavUser: React.FC = () => {
    const user = mockUser;

    return (
        <SidebarMenu>
            <SidebarMenuItem>
                <SidebarMenuButton
                    size="lg"
                    className="h-12 rounded-2xl transition-all hover:bg-muted data-[state=open]:bg-muted"
                >
                    <UserInfo user={user} />
                    <ChevronsUpDown className="ml-auto size-3.5 opacity-50" />
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    );
};
