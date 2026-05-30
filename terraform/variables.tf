variable "aws_region" {
  default = "ap-southeast-1"
}

variable "app_name" {
  default = "smartpos"
}

variable "environment" {
  default = "prod"
}

variable "ec2_instance_type" {
  default = "t3.small"
}

variable "db_instance_class" {
  default = "db.t3.micro"
}

variable "db_name" {
  default = "smartpos"
}

variable "db_username" {
  default = "smartpos_user"
}

variable "db_password" {
  sensitive = true
}

variable "domain_name" {
  description = "e.g. pos.yourcompany.com"
}

variable "key_pair_name" {
  description = "EC2 SSH key pair name (must already exist in AWS)"
}

variable "alert_email" {
  description = "Email address for CloudWatch alarm notifications"
}

variable "zone_name" {
  description = "Root domain for Route 53 hosted zone e.g. sahasralabs.click"
  default     = "sahasralabs.click"
}