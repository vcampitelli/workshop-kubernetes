import express, {Request, Response} from 'express';
import basicAuth, {IBasicAuthedRequest} from 'express-basic-auth';
import {importPKCS8, SignJWT, KeyLike, JWTPayload} from 'jose';
import {UserInterface} from './src/types';
const morgan = require('morgan');

/**
 * Checando variáveis de ambiente
 */
if (!(process.env.JWT_PRIVATE_KEY || '').length) {
    throw new Error('Variável de ambiente não definida: JWT_PRIVATE_KEY');
}

const UserRepository = require('./src/UserRepository');
const app = express();
app.use(morgan('combined'));

const userRepository = new UserRepository();
const authUsers: { [key: string]: string } = {};
userRepository.findAll().map((user: UserInterface) => {
    authUsers[user.username] = user.password;
});

app.use(basicAuth({
    users: authUsers
}));

let privateKey: KeyLike | undefined;

/**
 * Returns the private key
 *
 * @returns {Promise<{type: KeyLike}>}
 */
const getPrivateKey = async () => {
    if (!privateKey) {
        privateKey = await importPKCS8(
            process.env.JWT_PRIVATE_KEY,
            'ES256'
        );
    }

    return privateKey;
};

/**
 * Main endpoint (POST /auth)
 */
app.post('/auth', async (req: IBasicAuthedRequest | Request, res: Response) => {
    let user: UserInterface | undefined;
    if (("auth" in req) && (req.auth.user)) {
        user = userRepository.findByUsername(req.auth.user);
    }
    if (!user) {
        res.status(403).send();
        return;
    }

    const privateKey = await getPrivateKey();
    const payload: JWTPayload = {};
    if (user.scopes) {
        payload.scopes = user.scopes;
    }

    const jwt = await new SignJWT(payload)
        .setProtectedHeader({alg: 'ES256'})
        .setIssuedAt()
        .setIssuer('Workshop')
        .setAudience(user.username)
        .setExpirationTime('2h')
        .sign(privateKey);

    res.send({
        status: true,
        access_token: jwt
    });
});

app.listen(process.env.PORT || 3000);
