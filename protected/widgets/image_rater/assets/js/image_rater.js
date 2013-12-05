$.widget("custom.image_rater", {
    options: {
        id: "id",
        imageFile: "imageFile",
        image: "image",
        imageSrc: "",
    },
    id: function(id) {
        if (id === undefined) {
            return this.options.id;
        }
        this.options.id = id;

    },
    image: function(image) {
        if (image === undefined) {
            return this.options.image;
        }
        this.options.imageFile = image;
    },
    imageFile: function(imageFile) {
        if (imageFile === undefined) {
            return this.options.imageFile;
        }
        this.options.imageFile = imageFile;

    },
    _cssDiv:{width:"33%",margin:"0 auto",display:"inline",float:"left"},
    _cssInputText:{width:"20px"},
    _create: function() {
//        this.options.image =
                this.element
                .addClass("image_rator")
                .append(
                        $("<div></div>").append(
                        $("<img>")
                        .attr("id", this.options.id + "_image")
                        .attr("alt", "Image Preview")
                        .addClass("image_rator_image")
                        .attr("src",this.options.imageSrc)
                        )
                        )
                .append(
                        $("<div></div>").append(
                        $("<input>")
                        .attr("type", "file")
                        .attr("name", this.options.id +"_" + this.options.imageFile)
                        .attr("id", this.options.id + "_imageFile")
                        )
                        )
//                        .append($("<div></div>").css({clear:"both",display:"inline"}))
        $("input#" + this.options.id + "_imageFile").change({imageId: this.options.id + "_image"},
        function(evt) {
//            console.log("change image");
//            console.log(evt);
//            console.log(evt.data.imageId);
            input = evt.target

            if (input.files && input.files[0] && evt.data.imageId) {
                console.log("run");
                var reader = new FileReader();

                reader.onload = function(e) {
                    $("img#" + evt.data.imageId).attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
        );

        $("div#" + this.options.id + "_slider").slider()
    },
    _refresh: function() {
        $("input#" + this.options.id + "_imageFile").attr("name", this.options.imageFile)
    },
    _updateImage: function(input) {
        console.log("image");
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $("img#" + this.options.id + "_image").attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
});