/**
 * @link https://stackoverflow.com/a/53981706
 */
declare global {
    namespace NodeJS {
        interface ProcessEnv {
            NODE_ENV: 'development' | 'production';
            PORT?: string;
            JWT_PRIVATE_KEY: string;
        }
    }
}

export interface UserInterface {
    id: number;
    username: string;
    password: string;
    scopes: string[];
}
