output "ec2_public_ip" {
  value       = aws_eip.app.public_ip
  description = "Elastic IP of the EC2 instance"
}

output "rds_endpoint" {
  value       = aws_db_instance.postgres.endpoint
  description = "RDS PostgreSQL endpoint (host:port)"
}

output "s3_bucket_name" {
  value       = aws_s3_bucket.app.bucket
  description = "S3 bucket name for app storage"
}

output "app_url" {
  value       = "https://${var.domain_name}"
  description = "Application URL"
}
