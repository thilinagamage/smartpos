terraform {
  required_version = ">= 1.9"

  required_providers {
    aws = {
      source  = "hashicorp/aws"
      version = "~> 5.0"
    }
  }

  backend "s3" {
    bucket = "smartpos-terraform-state"
    key    = "smartpos/terraform.tfstate"
    region = "ap-southeast-1"
  }
}

provider "aws" {
  region = var.aws_region
}

module "networking" {
  source      = "./modules/networking"
  project     = var.project
  environment = var.environment
}

module "security" {
  source  = "./modules/security"
  project = var.project
  vpc_id  = module.networking.vpc_id
}

module "compute" {
  source            = "./modules/compute"
  project           = var.project
  subnet_id         = module.networking.public_subnet_id
  sg_id             = module.security.ec2_sg_id
  key_name          = var.key_name
  instance_type     = var.instance_type
  instance_profile  = module.security.instance_profile_name
}

module "database" {
  source             = "./modules/database"
  project            = var.project
  subnet_ids         = module.networking.private_subnet_ids
  sg_id              = module.security.rds_sg_id
  db_username        = var.db_username
  db_password        = var.db_password
  db_instance_class  = var.db_instance_class
}

module "storage" {
  source      = "./modules/storage"
  project     = var.project
  environment = var.environment
}
