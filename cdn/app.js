const express = require('express');
const fileUpload = require('express-fileupload');
const cors = require('cors');
const bodyParser = require('body-parser');
const morgan = require('morgan');
const _ = require('lodash');
const sharp = require('sharp');
const app = express();
const { v4: uuidV4 } = require('uuid');
const fs = require('fs');
const path = require('path');
// enable files upload

app.use(fileUpload({
    createParentPath: true
}));

//add other middleware
app.use(express.static('public'));
app.use(cors());
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({extended: true}));
app.use(morgan('dev'));

app.post('/upload', async (req, res) => {
    try {
        if(!req.files) {
            res.send({
                status: false,
                message: 'No file uploaded'
            });
        } else {
            //Use the name of the input field (i.e. "avatar") to retrieve the uploaded file
            let file = req.files.file;

            //Use the mv() method to place the file in the upload directory (i.e. "uploads")
            file.mv('./public/' + file.name).finally(() => {

                var fileExt = file.name.split('.').pop().toLowerCase();

                var uuid = uuidV4();

                if(['png', 'jpeg', 'jpg', 'bmp', 'gif'].includes(fileExt)) {
                    sharp('./public/' + file.name)
                        .toFile('./public/' + uuid + '.jpeg')
                        .then((data) => {
                            console.log(data);
                            fs.unlinkSync('./public/' + file.name);
                        }, err => console.log(err))
                    ;

                } else {
                    fs.renameSync('./public/' + file.name, './public/' + uuid + '.' + fileExt);
                }

                //send response
                res.send({
                    status: true,
                    message: 'File is uploaded',
                    data: {
                        name: uuid + '.' + (fileExt == 'jpg' ? 'jpeg' : fileExt),
                        mimetype: file.mimetype,
                        size: file.size
                    }
                });
            })


        }
    } catch (err) {
        res.status(500).send(err);
    }
});


//start app
const port = process.env.PORT || 3000;

app.listen(port, () =>
    console.log(`App is listening on port ${port}.`)
);