import * as React from 'react';
import { Link } from 'react-router-dom';
import { AppLogoIcon } from '@/components/AppLogo';

interface Props {
    title?: string;
    description?: string;
    children: React.ReactNode;
}

export const AuthLayout: React.FC<Props> = ({ title, description, children }) => {
    return (
        <div className="flex min-h-svh flex-col items-center justify-center gap-6 bg-background p-6 md:p-10">
            <div className="w-full max-w-sm">
                <div className="flex flex-col gap-8">
                    <div className="flex flex-col items-center gap-4">
                        <Link to="/" className="flex flex-col items-center gap-2 font-medium">
                            <div className="mb-1 flex h-9 w-9 items-center justify-center rounded-md p-2">
                                <AppLogoIcon className="size-9 fill-current text-foreground dark:text-white" />
                            </div>
                            <span className="sr-only">Posexei</span>
                        </Link>
                        <div className="space-y-2 text-center">
                            <h1 className="text-xl font-medium">{title}</h1>
                            <p className="text-center text-sm text-muted-foreground">{description}</p>
                        </div>
                    </div>
                    {children}
                </div>
            </div>
        </div>
    );
};

export default AuthLayout;
