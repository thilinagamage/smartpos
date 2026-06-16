variable "project" {
  description = "Project name"
  type        = string
}

variable "subnet_ids" {
  description = "List of private subnet IDs for the DB subnet group (needs at least 2 AZs)"
  type        = list(string)
}

variable "sg_id" {
  description = "Security group ID to attach to RDS"
  type        = string
}

variable "db_username" {
  description = "RDS master username"
  type        = string
}

variable "db_password" {
  description = "RDS master password"
  type        = string
  sensitive   = true
}

variable "db_instance_class" {
  description = "RDS instance class"
  type        = string
  default     = "db.t3.micro"
}
