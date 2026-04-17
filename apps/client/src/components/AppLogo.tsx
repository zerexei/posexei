import { cn } from '@/lib/utils';
import React from 'react';

interface AppLogoIconProps extends React.SVGProps<SVGSVGElement> {}

export const AppLogoIcon: React.FC<AppLogoIconProps> = ({ className, ...props }) => {
    return (
        <svg
            viewBox="0 0 24 24"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
            className={cn('fill-current', className)}
            {...props}
        >
            <path
                d="M13 2L3 14H12L11 22L21 10H12L13 2Z"
                fill="currentColor"
                stroke="currentColor"
                strokeWidth="2"
                strokeLinecap="round"
                strokeLinejoin="round"
            />
        </svg>
    );
};

export const AppLogo: React.FC = () => {
    return (
        <>
            <div className="flex aspect-square size-8 items-center justify-center rounded-xl p-2 bg-primary text-primary-foreground">
                <AppLogoIcon className="size-4" />
            </div>
            <div className="ml-1 grid flex-1 text-left text-sm">
                <span className="mb-0.5 truncate leading-tight font-semibold text-foreground">Posexei</span>
            </div>
        </>
    );
};
