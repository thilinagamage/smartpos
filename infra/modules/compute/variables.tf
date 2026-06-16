variable "project" {
  description = "Project name"
  type        = string
}

variable "subnet_id" {
  description = "Public subnet ID to launch EC2 into"
  type        = string
}

variable "sg_id" {
  description = "Security group ID to attach to EC2"
  type        = string
}

variable "key_name" {
  description = "EC2 key pair name for SSH access"
  type        = string
}

variable "instance_type" {
  description = "EC2 instance type"
  type        = string
  default     = "t3.small"
}

variable "instance_profile" {
  description = "IAM instance profile name to attach"
  type        = string
}
