import ProfileController from './ProfileController'
import PasswordController from './PasswordController'
import BillingController from './BillingController'

const Settings = {
    ProfileController: Object.assign(ProfileController, ProfileController),
    PasswordController: Object.assign(PasswordController, PasswordController),
    BillingController: Object.assign(BillingController, BillingController),
}

export default Settings