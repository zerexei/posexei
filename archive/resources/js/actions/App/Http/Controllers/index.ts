import PostController from './PostController'
import Settings from './Settings'
import Social from './Social'
import Auth from './Auth'

const Controllers = {
    PostController: Object.assign(PostController, PostController),
    Settings: Object.assign(Settings, Settings),
    Social: Object.assign(Social, Social),
    Auth: Object.assign(Auth, Auth),
}

export default Controllers