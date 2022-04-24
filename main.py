from model import *
from data import *
#os.environ["CUDA_VISIBLE_DEVICES"] = "0"


# Prepare data
images = glob.glob(r'.\data - Copy\train\aug\image\*.png')
images_mask = glob.glob(r'.\data - Copy\train\aug\label\*.png')
images_test = glob.glob(r'.\data - Copy\test\image\*.png')
X_train = []
y_train = []
x_test = []
for fl in images:
    img = cv2.imread(fl, 0)
    img = cv2.resize(img,(256,256),interpolation = cv2.INTER_AREA)
    X_train.append(img)
    
for fl in images_mask:
    img = cv2.imread(fl, 0)
    img = cv2.resize(img,(256,256),interpolation = cv2.INTER_AREA)
    y_train.append(img)
    
for fl in images_test:
    img = cv2.imread(fl, 0)
    img = cv2.resize(img,(256,256),interpolation = cv2.INTER_AREA)
    x_test.append(img)

X_train = np.array(X_train, dtype=np.uint8)
X_train = X_train.reshape(X_train.shape[0], 256, 256,1)
y_train = np.array(y_train, dtype=np.uint8)
y_train = y_train.reshape(y_train.shape[0], 256, 256,1)
X_train = X_train.astype('float32')
X_train /= 255
y_train = y_train.astype('float32')
y_train /= 255

x_test = np.array(x_test, dtype=np.uint8)
x_test = x_test.reshape(x_test.shape[0], 256, 256,1)
x_test = x_test.astype('float32')
x_test /= 255



model = unet()
model_checkpoint = ModelCheckpoint('unet_membrane.hdf5', monitor='loss',verbose=1, save_best_only=True)
model.fit(X_train,y_train,epochs=2,batch_size = 10,callbacks=[model_checkpoint])

results = model.predict(x_test)

results_class = results.argmax(axis=-1)
saveResult("data/membrane/test",results)



    
  
    
  

