output "vpc_id" {
  description = "ID of the VPC"
  value       = aws_vpc.main.id
}

output "public_subnet_id" {
  description = "ID of the public subnet (EC2 lives here)"
  value       = aws_subnet.public.id
}

output "private_subnet_ids" {
  description = "IDs of the private subnets (RDS lives here)"
  value       = [aws_subnet.private_a.id, aws_subnet.private_b.id]
}
