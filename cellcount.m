clear
clc
close all
warning off
% %for single image processing
% [currentFileName,PathName] = uigetfile('*','Select tif file');
% cd (PathName)

% select path of image file folder
selpath = uigetdir(path)
cd(selpath)
tifFiles = dir('*.jpg'); %get list of all .jpg file
nfiles = length(tifFiles);

% set signal detection parameters
minR = 3;               % minimum radius
maxR = 6;               % maximum radius
sen = 0.96;             % number of singles (0.9 ~ 1.0). More signals if close to 1. Less signals if close to 0.9
edgeThreshold = 0.1;    % gredient level consider as edge. Keep constant for batch processing

% file name to save
fileNameSave = strcat('results-minR',num2str(minR),'-maxR',num2str(maxR),'-Sen',num2str(sen),'.xlsx');
for ii = 1:nfiles
    %
    currentFileName = tifFiles(ii).name
    
    % convert to uint8
    I = imread(currentFileName);
    if isa(I,'uint16')
        Imax = 2^16/max(I(:));
        I = im2uint8(I)*double(Imax);
    end
    
    % invert if signals are darker than background
    % I = imcomplement(I);
    
    % show image
    figure
    imshow(I)
    % imshow(I(:,:,1))
    % I = imrotate(I,90);
    
    % copy image for preprocessing
    imageCopy =I(:,:,2);
    % imageCopy = imadjust(imageCopy);
    % figure
    % imshow(imageCopy)
    [m, n] = size(imageCopy);
    
    % remove background with rolling ball method
    background = imopen(imageCopy,strel('disk',20));
    imageCopy = imageCopy - background;
    
    % select ROI
    % mask= roipoly(imageCopy);
    % imageCopy(~mask) = 0;
    
    % detect signals
    [centers,radii] = imfindcircles(imageCopy,[minR maxR],'Sensitivity',sen,'EdgeThreshold',edgeThreshold);
    
    
    % remove false signals
    % centers(indexRemove,:) = [];
    % radii(indexRemove) = [];
    % for i = length(radii):-1:1
    %     if imageCopy(round(centers(i,2)),round(centers(i,1)))<50
    %         centers(i,:) = [];
    %         radii(i) = [];
    %     end
    %
    % end
    
    % show final results
    figure
    imshow(I)
    % pause(0.2)
    viscircles(centers, radii,'Color','b');
    drawnow
    
    % save image result
    saveas(gcf,strcat(currentFileName(1:end-4),'-figure.fig'))
    
    % save signal coordinates and radius
    xlswrite(strcat(currentFileName(1:end-4),'.xlsx'),[centers radii],'Sheet1','A1')
    
    % save signals numbers
    if ~exist(fileNameSave)
        xlswrite(fileNameSave,{'Name','numberOfCells'},'Sheet1','A1')
        xlswrite(fileNameSave,{currentFileName},'Sheet1','A2')
        xlswrite(fileNameSave,length(radii),'Sheet1','B2')
    else
        [~,~,raw] = xlsread(fileNameSave);
        [nl,~] = size(raw);
        xlswrite(fileNameSave,{currentFileName},'Sheet1',strcat('A',num2str(nl+1)))
        xlswrite(fileNameSave,length(radii),'Sheet1',strcat('B',num2str(nl+1)))
    end
end