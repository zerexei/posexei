output "eks_cluster_name" {
  value = module.kubernetes.cluster_name
}

output "eks_cluster_endpoint" {
  value = module.kubernetes.cluster_endpoint
}

output "db_endpoint" {
  value = module.database.db_endpoint
}

output "redis_endpoint" {
  value = module.redis.redis_endpoint
}

output "storage_bucket" {
  value = module.storage.storage_bucket_name
}

output "loki_bucket" {
  value = module.storage.loki_bucket_name
}

output "tempo_bucket" {
  value = module.storage.tempo_bucket_name
}
