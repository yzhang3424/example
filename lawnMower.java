import java.util.*;

public class lawnMower {
    private static Random randGenerator;
    private static final int DEFAULT_WIDTH = 200;
    private static final int DEFAULT_HEIGHT = 200;

    private Integer[][] mowerLawnInfo;
    private boolean[][] isPathExplored;

    private int[][] lawnCutSeq;

    private int pathSeq = 1;
    private boolean isScan = true;
    private boolean isCrush = false;

    private Integer mowerXBlind, mowerYBlind;
    private String mowerDirection;

    private HashMap<String, Integer> xDIR_MAP;
    private HashMap<String, Integer> yDIR_MAP;
    private static final String[] DIRECTIONS = {"North","Northeast","East","Southeast","South","Southwest","West","Northwest"};

    private String mowerAction;
    private Integer mowerMoveDistance;
    private String mowerNewDirection;
    private int[] destinationLocation = new int[2];
    private int mowerDestinationDiffX;
    private int mowerDestinationDiffY;

    private final int EMPTY_CODE = 0;
    private final int GRASS_CODE = 1;
    private final int CRATER_CODE = 2;
    private final int FENCE_CODE = 3;
    private final int UNDETECT_CODE = 4;

    List<Coordinate> mowerPath = new ArrayList<>();

    public lawnMower(){
        randGenerator = new Random();
        int i,j;
        mowerDirection = "North";

        xDIR_MAP = new HashMap<>();
        xDIR_MAP.put("North", 0);
        xDIR_MAP.put("Northeast", 1);
        xDIR_MAP.put("East", 1);
        xDIR_MAP.put("Southeast", 1);
        xDIR_MAP.put("South", 0);
        xDIR_MAP.put("Southwest", -1);
        xDIR_MAP.put("West", -1);
        xDIR_MAP.put("Northwest", -1);

        yDIR_MAP = new HashMap<>();
        yDIR_MAP.put("North", 1);
        yDIR_MAP.put("Northeast", 1);
        yDIR_MAP.put("East", 0);
        yDIR_MAP.put("Southeast", -1);
        yDIR_MAP.put("South", -1);
        yDIR_MAP.put("Southwest", -1);
        yDIR_MAP.put("West", 0);
        yDIR_MAP.put("Northwest", 1);

        mowerLawnInfo = new Integer[DEFAULT_WIDTH][DEFAULT_HEIGHT];
        lawnCutSeq = new int[DEFAULT_WIDTH][DEFAULT_HEIGHT];
        isPathExplored = new boolean[DEFAULT_WIDTH][DEFAULT_HEIGHT];
        mowerXBlind = 100;
        mowerYBlind = 100;
        for (i = 0; i < DEFAULT_WIDTH; i++) {
            for (j = 0; j < DEFAULT_HEIGHT; j++) {
                mowerLawnInfo[i][j] = UNDETECT_CODE;
                lawnCutSeq[i][j] = Integer.MAX_VALUE;
                isPathExplored[i][j] = false;
            }
        }
    }

    public List<Coordinate> pathFinder(Integer[][] mowerLawnInfo,boolean[][] isPathExplored){
        boolean[][] isExplored = new boolean[DEFAULT_WIDTH][DEFAULT_HEIGHT];
                isExplored = isPathExplored;
        List<Coordinate> path = new ArrayList<>();
        if(findPath(mowerLawnInfo,mowerXBlind,mowerYBlind,path,isExplored)){
            return path;
        }
        return Collections.emptyList();
    }

    public boolean findPath(Integer[][] mowerLawnInfo, int X, int Y, List<Coordinate> path, boolean[][] isExplored){
        if(mowerLawnInfo[X][Y] != GRASS_CODE & mowerLawnInfo[X][Y] != EMPTY_CODE || isExplored[X][Y]){
            return false;
        }
        path.add(new Coordinate(X,Y));
        isExplored[X][Y] = true;
        if(destinationLocation[0] == X & destinationLocation[1] == Y ){
            return true;
        }
        for(String direction : DIRECTIONS){
            Coordinate coordinate = getNextCoordinate(X,Y,xDIR_MAP.get(direction),yDIR_MAP.get(direction));
            if(findPath(mowerLawnInfo,coordinate.getX(),coordinate.getY(),path,isExplored)){
                return true;
            }
        }
        path.remove(path.size()-1);
        return false;
    }

    private Coordinate getNextCoordinate(int X, int Y, int x, int y){
        return new Coordinate(X+x,Y+y);
    }


    public String mowerActionChoose() {
        int moveRandomChoice;
        if (isCrush) {
            // select turning off the mower as the action
            mowerAction = "turn_off";
            return mowerAction;
        } else if (isScan) {
            // select scanning as the action
            mowerAction = "scan";
            isScan = false;
            return mowerAction;
        } else {
            // select moving forward and the turning as the action
            mowerAction = "move";
            mowerMoveDistance = 1;

            // determine a new direction
            mowerDestinationDiffX = destinationLocation[0] - mowerXBlind;
            mowerDestinationDiffY = destinationLocation[1] - mowerYBlind;

            if (mowerDestinationDiffX == 0 && mowerDestinationDiffY > 0){
                mowerNewDirection = "North";
            }
            if (mowerDestinationDiffX > 0 && mowerDestinationDiffY > 0){
                mowerNewDirection = "Northeast";
            }
            if (mowerDestinationDiffX > 0 && mowerDestinationDiffY == 0){
                mowerNewDirection = "East";
            }
            if (mowerDestinationDiffX > 0 && mowerDestinationDiffY < 0){
                mowerNewDirection = "Southeast";
            }
            if (mowerDestinationDiffX == 0 && mowerDestinationDiffY < 0){
                mowerNewDirection = "South";
            }
            if (mowerDestinationDiffX < 0 && mowerDestinationDiffY < 0){
                mowerNewDirection = "Southwest";
            }
            if (mowerDestinationDiffX < 0 && mowerDestinationDiffY == 0){
                mowerNewDirection = "West";
            }
            if (mowerDestinationDiffX < 0 && mowerDestinationDiffY > 0){
                mowerNewDirection = "Northwest";
            }
            int xDirection = xDIR_MAP.get(mowerNewDirection);
            int yDirection = yDIR_MAP.get(mowerNewDirection);
            int potentialSquareX = mowerXBlind + mowerMoveDistance * xDirection;
            int potentialSquareY = mowerYBlind + mowerMoveDistance * yDirection;
            while(mowerLawnInfo[potentialSquareX][potentialSquareY]==CRATER_CODE || mowerLawnInfo[potentialSquareX][potentialSquareY]==FENCE_CODE){
                moveRandomChoice = randGenerator.nextInt(100);
                if(moveRandomChoice<50) {
                    switch (mowerNewDirection) {
                        case "South":
                            mowerNewDirection = "Southwest";
                            break;
                        case "Southwest":
                            mowerNewDirection = "West";
                            break;
                        case "West":
                            mowerNewDirection = "Northwest";
                            break;
                        case "Northwest":
                            mowerNewDirection = "North";
                            break;
                        case "Southeast":
                            mowerNewDirection = "South";
                            break;
                        case "North":
                            mowerNewDirection = "Northeast";
                            break;
                        case "Northeast":
                            mowerNewDirection = "East";
                            break;
                        case "East":
                            mowerNewDirection = "Southeast";
                            break;
                        default:
                            mowerNewDirection = mowerDirection;
                            break;
                    }
                }else {
                    switch (mowerNewDirection) {
                        case "South":
                            mowerNewDirection = "Southeast";
                            break;
                        case "Southeast":
                            mowerNewDirection = "East";
                            break;
                        case "East":
                            mowerNewDirection = "Northeast";
                            break;
                        case "Northeast":
                            mowerNewDirection = "North";
                            break;
                        case "North":
                            mowerNewDirection = "Northwest";
                            break;
                        case "Northwest":
                            mowerNewDirection = "West";
                            break;
                        case "West":
                            mowerNewDirection = "Southwest";
                            break;
                        case "Southwest":
                            mowerNewDirection = "South";
                            break;
                        default:
                            mowerNewDirection = mowerDirection;
                            break;
                    }
                }
                xDirection = xDIR_MAP.get(mowerNewDirection);
                yDirection = yDIR_MAP.get(mowerNewDirection);
                potentialSquareX = mowerXBlind + mowerMoveDistance * xDirection;
                potentialSquareY = mowerYBlind + mowerMoveDistance * yDirection;
            }

            mowerDirection = mowerNewDirection;
            return mowerAction + "," + mowerDirection;
        }

    }

    public void mowerAction(int[] scanSeq) {
        int xOrientation, yOrientation;
        if (mowerAction.equals("scan") & scanSeq == null) {
            mowerAction = "turn_off";
        }
        if (mowerAction.equals("scan") ) {
            // in the case of a scan, return the information for the eight surrounding squares
            // always use a northbound orientation

            mowerLawnInfo[mowerXBlind][mowerYBlind+1] = scanSeq[0];
            mowerLawnInfo[mowerXBlind+1][mowerYBlind+1] = scanSeq[1];
            mowerLawnInfo[mowerXBlind+1][mowerYBlind] = scanSeq[2];
            mowerLawnInfo[mowerXBlind+1][mowerYBlind-1] = scanSeq[3];
            mowerLawnInfo[mowerXBlind][mowerYBlind-1] = scanSeq[4];
            mowerLawnInfo[mowerXBlind-1][mowerYBlind-1] = scanSeq[5];
            mowerLawnInfo[mowerXBlind-1][mowerYBlind] = scanSeq[6];
            mowerLawnInfo[mowerXBlind-1][mowerYBlind+1] = scanSeq[7];


            if (scanSeq[0] == GRASS_CODE && lawnCutSeq[mowerXBlind][mowerYBlind+1] == Integer.MAX_VALUE) {
                lawnCutSeq[mowerXBlind][mowerYBlind+1] = pathSeq;
                pathSeq++;
            }
            if (scanSeq[1] == GRASS_CODE && lawnCutSeq[mowerXBlind+1][mowerYBlind+1] == Integer.MAX_VALUE) {
                lawnCutSeq[mowerXBlind+1][mowerYBlind+1] = pathSeq;
                pathSeq++;
            }
            if (scanSeq[2] == GRASS_CODE && lawnCutSeq[mowerXBlind+1][mowerYBlind] == Integer.MAX_VALUE) {
                lawnCutSeq[mowerXBlind+1][mowerYBlind] = pathSeq;
                pathSeq++;
            }
            if (scanSeq[3] == GRASS_CODE && lawnCutSeq[mowerXBlind+1][mowerYBlind-1] == Integer.MAX_VALUE) {
                lawnCutSeq[mowerXBlind+1][mowerYBlind-1] = pathSeq;
                pathSeq++;
            }
            if (scanSeq[4] == GRASS_CODE && lawnCutSeq[mowerXBlind][mowerYBlind-1] == Integer.MAX_VALUE) {
                lawnCutSeq[mowerXBlind][mowerYBlind-1] = pathSeq;
                pathSeq++;
            }
            if (scanSeq[5] == GRASS_CODE && lawnCutSeq[mowerXBlind-1][mowerYBlind-1] == Integer.MAX_VALUE) {
                lawnCutSeq[mowerXBlind-1][mowerYBlind-1] = pathSeq;
                pathSeq++;
            }
            if (scanSeq[6] == GRASS_CODE && lawnCutSeq[mowerXBlind-1][mowerYBlind] == Integer.MAX_VALUE) {
                lawnCutSeq[mowerXBlind-1][mowerYBlind] = pathSeq;
                pathSeq++;
            }
            if (scanSeq[7] == GRASS_CODE && lawnCutSeq[mowerXBlind-1][mowerYBlind+1] == Integer.MAX_VALUE) {
                lawnCutSeq[mowerXBlind-1][mowerYBlind+1] = pathSeq;
                pathSeq++;
            }
            destinationLocation = findMinValue(lawnCutSeq);
            mowerPath = pathFinder(mowerLawnInfo,isPathExplored);

        } else if (mowerAction.equals("move")) {
            // in the case of a move, ensure that the move doesn't cross craters or fences
            xOrientation = xDIR_MAP.get(mowerDirection);
            yOrientation = yDIR_MAP.get(mowerDirection);

            //mowerDirection = trackNewDirection;

            int newSquareX = mowerXBlind + mowerMoveDistance * xOrientation;
            int newSquareY = mowerYBlind + mowerMoveDistance * yOrientation;

            if (mowerLawnInfo[newSquareX][newSquareY] == EMPTY_CODE || mowerLawnInfo[newSquareX][newSquareY] == GRASS_CODE) {
                mowerXBlind = newSquareX;
                mowerYBlind = newSquareY;

                if(mowerLawnInfo[mowerXBlind][mowerYBlind] == GRASS_CODE) {
                    isScan = true;

                    // update lawn status
                    mowerLawnInfo[mowerXBlind][mowerYBlind] = EMPTY_CODE;
                    lawnCutSeq[mowerXBlind][mowerYBlind] = Integer.MAX_VALUE;
                    destinationLocation = findMinValue(lawnCutSeq);
                    mowerPath = pathFinder(mowerLawnInfo,isPathExplored);
                }
            } else {
                isCrush = true;
            }
        } else if (mowerAction.equals("turn_off")){
            return;
        }
    }





    private static int[] findMinValue(int[][] array2D) {
        int minValue = array2D[0][0];
        int[] xyIndex = new int[2];
        xyIndex[0] = 0;
        xyIndex[1] = 0;
        int yIndex = 0;
        for (int j = 0; j < array2D.length; j++) {
            for (int i = 0; i < array2D[j].length; i++) {
                if (array2D[j][i] < minValue ) {
                    minValue = array2D[j][i];
                    xyIndex[0] = j;
                    xyIndex[1] = i;
                }
            }
        }
        return xyIndex;
    }

}
