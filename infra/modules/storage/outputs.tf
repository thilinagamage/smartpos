output "bucket_name" {
  description = "S3 bucket name - use this as AWS_BUCKET in .env"
  value       = aws_s3_bucket.uploads.id
}

output "bucket_arn" {
  description = "S3 bucket ARN"
  value       = aws_s3_bucket.uploads.arn
}
