CXX = g++
CXXFLAGS = -O3 -std=c++11 -I./rapidjson/include #-O2 for perfomance

# Source files
SRC_DOT = dot.cpp Utility.cpp Matrix.cpp
SRC_ADD = add.cpp Utility.cpp Matrix.cpp
SRC_JACOBIAN = jacobianMatrix.cpp Utility.cpp

# Targets
TARGET_DOT = dot
TARGET_ADD = add
TARGET_JACOBIAN = jacobianMatrix

all: $(TARGET_DOT) $(TARGET_ADD)

$(TARGET_DOT):
	$(CXX) $(CXXFLAGS) $(SRC_DOT) -o $(TARGET_DOT)

$(TARGET_ADD):
	$(CXX) $(CXXFLAGS) $(SRC_ADD) -o $(TARGET_ADD)

$(TARGET_JACOBIAN):
	$(CXX) $(CXXFLAGS) $(SRC_JACOBIAN) -o $(TARGET_JACOBIAN)

clean:
	rm -f $(TARGET_DOT) $(TARGET_ADD) $(TARGET_JACOBIAN)

.PHONY: all clean
