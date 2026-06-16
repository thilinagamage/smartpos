output "ec2_sg_id" {
  description = "ID of the EC2 security group"
  value       = aws_security_group.ec2.id
}

output "rds_sg_id" {
  description = "ID of the RDS security group"
  value       = aws_security_group.rds.id
}

output "instance_profile_name" {
  description = "Name of the IAM instance profile to attach to EC2"
  value       = aws_iam_instance_profile.ec2.name
}
