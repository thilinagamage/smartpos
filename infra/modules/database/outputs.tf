output "db_endpoint" {
  description = "RDS endpoint hostname - use this as DB_HOST in .env"
  value       = aws_db_instance.postgres.address
}

output "db_port" {
  description = "RDS port"
  value       = aws_db_instance.postgres.port
}
