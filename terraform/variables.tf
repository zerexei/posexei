variable "aws_region" {
  description = "AWS region to deploy resources"
  type        = "string"
  default     = "us-east-1"
}

variable "project_name" {
  description = "Name of the project"
  type        = "string"
  default     = "posexei"
}

variable "environment" {
  description = "Deployment environment (e.g. prod, staging)"
  type        = "string"
  default     = "prod"
}

variable "vpc_cidr" {
  description = "CIDR block for the VPC"
  type        = "string"
  default     = "10.0.0.0/16"
}

variable "db_name" {
  type    = string
  default = "posexei"
}

variable "db_username" {
  type    = string
  default = "postgres"
}

variable "db_password" {
  type      = string
  sensitive = true
}
