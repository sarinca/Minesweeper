-- Table 1: User
CREATE TABLE user(email VARCHAR(60) NOT NULL,
                  username VARCHAR(60) NOT NULL, -- superkey but not pk?
                  password VARCHAR(255) NOT NULL
                  PRIMARY KEY (email)
                  UNIQUE (username)); -- is username unique?

-- Table 2: Profile
CREATE TABLE profile(userId INT AUTO_INCREMENT, -- not null implied im p sure
                     username VARCHAR(60) NOT NULL, -- FK/references from User table?
                     points INT Default 0, -- not null implied i think
                     totalScore INT Default 0,
                     profilePicture_path VARCHAR(255), -- just store path to image?
                     PRIMARY KEY (userId)
                     UNIQUE (username), -- idk if this line would cause problems if we didn't include it
                     FOREIGN KEY (username) REFERENCES user(username) -- makes profile a child table and user the parent
                        ON DELETE CASCADE -- if a row in user is deleted, then matching rows in profile are deleted
                        ON UPDATE CASCADE); -- same ^ but with updating if we wanna update usernames?. I assumed this structure because youd probably 
                                            -- delete a user not a profile, but deleting a user would delete the profile of that user
    
-- Table 3: Friends
CREATE TABLE friends(userId1 INT references profile(userId),
                     userId2 INT references profile(userId),
                     PRIMARY KEY (userId1, userId2));
    
-- Table 4: Price
CREATE TABLE price(name VARCHAR(60) NOT NULL,
                   description VARCHAR(255) NOT NULL,
                   price INT NOT NULL, -- not a decimal bc points are ints
                   PRIMARY KEY (name)); 

-- Table 5: Item
CREATE TABLE item(itemId INT AUTO_INCREMENT,
                  name VARCHAR(60) NOT NULL,
                  PRIMARY KEY (itemId),
                  FOREIGN KEY (name) REFERENCES price(name)); -- is this right or should i just do references?

-- Table 6: buys
CREATE TABLE buys(userId INT references profile(userId),
                  itemId INT references item(itemId),
                  PRIMARY KEY (userId, itemId));

-- Table 7: Leaderboard Entry
CREATE TABLE leaderboardEntry(entryId INT AUTO_INCREMENT,
                              rank INT NOT NULL, -- unique
                              gameId INT NOT NULL, -- unique
                              PRIMARY KEY (entryId));

-- Table 8: Populates
CREATE TABLE populates(entryId INT references leaderboardEntry(entryId),
                       userId INT references profile(userId),
                       PRIMARY KEY (entryId));

-- Table 9: Gamemode
CREATE TABLE gamemode(mode VARCHAR(60) NOT NULL,
                      width INT NOT NULL,
                      height INT NOT NULL,
                      numBombs INT NOT NULL,
                      points INT NOT NULL,
                      PRIMARY KEY (mode));

-- Table 10: Board state
CREATE TABLE state(state_boxesClicked INT Default 0,
                   state_bombPlacement VARCHAR(255) NOT NULL, -- storred as string right?
                   state_status VARCHAR(30) NOT NULL,
                   PRIMARY KEY (state_boxesClicked, state_bombPlacement));

-- Table 11: Game
CREATE TABLE game(userId INT references profile(userId),
                  gameId INT references leaderboardEntry(gameId),
                  state_boxesClicked INT,
                  state_bombPlacement VARCHAR(255),
                  mode VARCHAR(60) references gamemode(mode),
                  gameTime DATETIME NOT NULL, -- default val?
                  PRIMARY KEY (userId, gameId)
                  FOREIGN KEY (state_boxesClicked, state_bombPlacement) references state(state_boxesClicked, state_bombPlacement));
                  -- i did it this way bc i think the two states should be referenced together?
                  -- i was also considering that maybe the reference has to happen in the state table to the game table instead of this way?


    -- reqId INT AUTO_INCREMENT, 
    --                   reqDate DATE NOT NULL,
    --                   roomNumber VARCHAR(30), 
    --                   reqBy VARCHAR(60) NOT NULL,
    --                   repairDesc VARCHAR(255) NOT NULL,
    --                   reqPriority VARCHAR(10),
    --                   PRIMARY KEY (reqId));