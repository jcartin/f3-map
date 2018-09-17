#! /usr/bin/ruby
require 'yaml'

Dir.glob("*.zip").each do |zip|
    puts "deleting existing file: #{zip}"
    File.delete(zip)
end

options = YAML.load_file('options.yaml')

puts "creating new plugin version: #{options[:version]}"
cmd = "zip -r f3-map-#{options[:version]}.zip . -x *.DS_Store *.git* *.gitignore wp-deploy.* wp-deploy.* *node_modules*"
puts cmd
system(cmd)
