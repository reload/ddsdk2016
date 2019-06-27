workflow "Validate" {
  on = "push"
  resolves = ["Composer validate"]
}

action "Composer validate" {
  uses = "pxgamer/composer-action@master"
  args = "validate --no-check-all --no-check-publish"
}
