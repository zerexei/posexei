module "network" {
  source = "./modules/network"

  project_name = var.project_name
  environment  = var.environment
  vpc_cidr      = var.vpc_cidr
  cluster_name  = "${var.project_name}-eks"
}

module "kubernetes" {
  source = "./modules/kubernetes"

  project_name = var.project_name
  environment  = var.environment
  cluster_name = "${var.project_name}-eks"
  vpc_id       = module.network.vpc_id
  subnet_ids   = module.network.private_subnets
}

module "database" {
  source = "./modules/database"

  project_name          = var.project_name
  environment           = var.environment
  vpc_id                = module.network.vpc_id
  private_subnets       = module.network.private_subnets
  eks_security_group_id = module.kubernetes.eks_security_group_id
  db_name               = var.db_name
  db_username           = var.db_username
  db_password           = var.db_password
}

module "redis" {
  source = "./modules/redis"

  project_name          = var.project_name
  environment           = var.environment
  vpc_id                = module.network.vpc_id
  private_subnets       = module.network.private_subnets
  eks_security_group_id = module.kubernetes.eks_security_group_id
}

module "storage" {
  source = "./modules/storage"

  project_name = var.project_name
  environment  = var.environment
}
