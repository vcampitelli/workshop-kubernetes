import { UserInterface } from './types';

module.exports = class UserRepository {

    /**
     * Users database
     *
     * @type {UserInterface[]}
     */
    #data: UserInterface[] = [
        {
            id: 1,
            username: 'admin',
            password: 'admin',
            scopes: ['users', 'posts', 'comments']
        },
        {
            id: 2,
            username: 'nousers',
            password: 'nousers',
            scopes: ['posts', 'comments']
        },
        {
            id: 3,
            username: 'nocomments',
            password: 'nocomments',
            scopes: ['users', 'posts']
        },
        {
            id: 4,
            username: 'onlyposts',
            password: 'onlyposts',
            scopes: ['posts']
        }
    ];

    /**
     * @returns {UserInterface[]}
     */
    findAll(): UserInterface[] {
        return this.#data;
    }

    /**
     * @param {string} username
     * @returns {UserInterface|null}
     */
    findByUsername(username: string): UserInterface | null {
        return this.#data.find((user) => user.username === username) || null;
    }

}
