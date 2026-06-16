data "aws_ami" "ubuntu" {
  most_recent = true
  owners      = ["099720109477"] # Canonical

  filter {
    name   = "name"
    values = ["ubuntu/images/hvm-ssd-gp3/ubuntu-noble-24.04-amd64-server-*"]
  }

  filter {
    name   = "virtualization-type"
    values = ["hvm"]
  }

  filter {
    name   = "architecture"
    values = ["x86_64"]
  }

  filter {
    name   = "state"
    values = ["available"]
  }
}

resource "aws_instance" "app" {
  ami                    = data.aws_ami.ubuntu.id
  instance_type          = var.instance_type
  subnet_id              = var.subnet_id
  vpc_security_group_ids = [var.sg_id]
  key_name               = var.key_name
  iam_instance_profile   = var.instance_profile

 user_data = <<-USERDATA
    #!/bin/bash
    # Wait for apt lock to be released (unattended-upgrades runs on first boot)
    while fuser /var/lib/dpkg/lock-frontend >/dev/null 2>&1; do
      echo "Waiting for apt lock..."
      sleep 5
    done

    apt-get update -y
    apt-get install -y docker.io docker-compose-plugin

    systemctl enable docker
    systemctl start docker
    usermod -aG docker ubuntu

    mkdir -p /home/ubuntu/smartpos
    chown ubuntu:ubuntu /home/ubuntu/smartpos

    echo "user_data complete" > /tmp/user_data_done.txt
  USERDATA

  tags = {
    Name    = "${var.project}-app"
    Project = var.project
  }
}

resource "aws_eip" "app" {
  instance = aws_instance.app.id
  domain   = "vpc"

  tags = {
    Name    = "${var.project}-eip"
    Project = var.project
  }
}
