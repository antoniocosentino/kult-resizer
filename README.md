# Kult Resizer

## What is this?
Kult Resizer is a simple PHP utility which can be used to resize images coming from an Amazon AWS S3 bucket. The images are resized and served on-the-fly but there's also a caching mechanism in order to optimize performance. It also supports subfolders, up to 1 level.
The tool is meant to run as a standalone microservice, however it can also be included in an existing PHP project.

### Setup

The easiest way to run this locally is with [Docker](https://www.docker.com).

First create a file on root level called `variables.env`

Then put here the environmental variables:


| Variable       | Description
| -------------  |:-------------
| `CACHE_FOLDER` | the name of the directory for the cached images. I recommend to use `cache`. Anyway, if you need to change it, also make sure you change it in `start.sh` otherwise the folder will not be created in the Docker container.
| `BUCKET_URL`   | the URL of your S3 bucket.
| `DEBUG_MODE`   | when something doesn't work it's difficult to understand what's going on. Set this to `1` to enable debug mode.


Example:
```
CACHE_FOLDER=cache
BUCKET_URL=https://s3.eu-central-1.amazonaws.com/atw-images
DEBUG_MODE=0
```

When this is done you can run it:

`docker-compose up --build`

#

### Kult Resizer at work

Now that the service is running you can see it at work.

Let's assume that you have configured your .ENV file as explained above, and that you have an image in your S3 bucket at this URL:
\
https://s3.eu-central-1.amazonaws.com/atw-images/amb_images/AMB_AS10000.jpg

If you want to retrieve this file, on a resolution of max 400 pixels, just use this link:
\
http://localhost:1811/amb_images/400/AMB_AS10000.jpg
\
This means that the image will be resized in a way that the bigger side will be 400px.
\
\
There is also a "contain" feature:
\
http://localhost:1811/amb_images/400x200/AMB_AS10000.jpg
\
This means that the image will have max. width of 400px and max. height of 200px. The resulting image will be a proportioned image fitting inside these dimensions.

#

### To-do
- Currently only JPGs are supported. PNGs should be supported as well.
- There is no cropping logic. It might be interesting to introduce a square-crop feature
- When the path is wrong a black image is outputted. It should instead fail in a more "standard" way (i.e. 404)
