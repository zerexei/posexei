resource "aws_s3_bucket" "app_storage" {
  bucket = "${var.project_name}-${var.environment}-storage"

  tags = {
    Environment = var.environment
  }
}

resource "aws_s3_bucket" "loki_storage" {
  bucket = "${var.project_name}-${var.environment}-loki"

  tags = {
    Environment = var.environment
  }
}

resource "aws_s3_bucket" "tempo_storage" {
  bucket = "${var.project_name}-${var.environment}-tempo"

  tags = {
    Environment = var.environment
  }
}
