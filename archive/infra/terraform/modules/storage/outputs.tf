output "storage_bucket_name" {
  value = aws_s3_bucket.app_storage.id
}

output "loki_bucket_name" {
  value = aws_s3_bucket.loki_storage.id
}

output "tempo_bucket_name" {
  value = aws_s3_bucket.tempo_storage.id
}
