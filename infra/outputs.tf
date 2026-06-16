output "ec2_public_ip" {
  description = "Elastic IP of the EC2 instance - use this as EC2_HOST in GitHub Secrets"
  value       = module.compute.elastic_ip
}

output "rds_endpoint" {
  description = "RDS PostgreSQL endpoint - use this as DB_HOST in .env"
  value       = module.database.db_endpoint
  sensitive   = true
}

output "s3_bucket_name" {
  description = "S3 bucket name - use this as AWS_BUCKET in .env"
  value       = module.storage.bucket_name
}

output "ssh_command" {
  description = "Ready-to-use SSH command to connect to EC2"
  value       = "ssh -i ~/.ssh/${var.key_name}.pem ubuntu@${module.compute.elastic_ip}"
}
